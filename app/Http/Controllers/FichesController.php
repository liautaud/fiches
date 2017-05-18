<?php

namespace App\Http\Controllers;

use App\Entities\Exercice;
use App\Entities\Fiche;
use App\Entities\Langage;
use App\Entities\Utilisateur;

use App\Jobs\SynchroniserFicheGit;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ORM\Facades\EntityManager as Manager;
use Illuminate\Http\Request;

class FichesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fiches = Fiche::allPositive();
        $langages = Langage::all();
        
        return view('gestion.fiches.index', compact('fiches', 'langages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit(new Fiche(), true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Fiche(), true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Fiche $fiche
     * @return \Illuminate\Http\Response
     */
    public function edit(Fiche $fiche, $ajout = false)
    {
        $exercices = $fiche->getExercices()->map(function (Exercice $exercice) {
            $value = $exercice->getType();
            $text = ($value == 'texte') ? 'Texte' : 'Code ' . strtoupper($value);

           return compact('value', 'text');
        })->toArray();

        $dependances = $fiche->getPrecedentes()->filter(function (Fiche $fiche) {
            return $fiche->getId() > 0;
        })->map(function (Fiche $fiche) {
            $value = $fiche->getId();
            $text = $fiche->getTitre();

            return compact('value', 'text');
        })->toArray();

        return view('gestion.fiches.edit', compact('fiche', 'ajout', 'exercices', 'dependances'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Fiche $fiche
     * @param  bool  $ajout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fiche $fiche, $ajout = false)
    {
        $this->validate($request, [
            'titre'   => 'required|max:255',
            'langage' => 'required|max:255',
            'chemin'  => 'required|max:255'
        ]);

        // On crée le langage si besoin.
        $titreLangage = $request->input('langage');
        $langage = Manager::getRepository(Langage::class)
            ->findOneBy(['titre' => $titreLangage]);

        if ( ! $langage) {
            $langage = new Langage();
            $langage->setTitre($titreLangage);

            Manager::persist($langage);
        }

        // On renseigne les informations de la fiche.
        $titre = $request->input('titre');
        $fiche->setTitre($titre);
        $fiche->setLangage($langage);
        $fiche->setCheminGit((string) $request->input('chemin'));
        $fiche->setCheminGitComplementaires((string) $request->input('chemin-complementaires'));
        $fiche->setMiniProjet($request->has('mini-projet'));

        // On renseigne les dépendances de la fiche.
        // Pour cela, on commence par retirer toutes les dépendances
        // précédentes, puis on ajoute les nouvelles une par une.
        $dependances = explode(',', (string) $request->input('dependances'));

        $fiche->clearPrecedentes();

        foreach ($dependances as $id) {
            $dependance = Manager::find(Fiche::class, $id);

            if ($dependance) {
                $fiche->addPrecedente($dependance);
            }
        }

        // Si la fiche n'a aucune dépendance, on la lie par convention
        // à la fiche racine, d'identifiant 0.
        if ($fiche->getPrecedentes()->isEmpty()) {
            $racine = Manager::find(Fiche::class, 0);

            $fiche->addPrecedente($racine);
        }

        // On renseigne les exercices de la fiche.
        // Pour cela, on procède de façon un peu plus intelligente en
        // essayant de minimiser le nombre de changements à apporter.
        if (empty($request->input('exercices'))) {
            $typesSouhaites = [];
        } else {
            $typesSouhaites = explode(',', (string) $request->input('exercices'));
        }

        $exercicesExistants = $fiche->getExercices()->toArray();

        // On itère en même temps sur les exercices souhaités et ceux
        // déjà existants à l'aide d'une boucle for.
        $max =  max(count($typesSouhaites), count($exercicesExistants));
        for ($i = 0; $i < $max; $i++) {
            if (isset ($exercicesExistants[$i]) && isset($typesSouhaites[$i])) {
                $exercicesExistants[$i]->setType($typesSouhaites[$i]);
            } else if (isset($typesSouhaites[$i])) {
                $exercice = new Exercice();
                $exercice->setFiche($fiche);
                $exercice->setNumero($i + 1);
                $exercice->setType($typesSouhaites[$i]);

                Manager::persist($exercice);
            } else if (isset ($exercicesExistants[$i])) {
                Manager::remove($exercicesExistants[$i]);
            }
        }

        Manager::persist($fiche);
        Manager::flush();

        $this->dispatch(new SynchroniserFicheGit($fiche));

        if ($ajout) {
            return redirect()
                ->route('fiches.index')
                ->with('message', 'La fiche ' . $titre . ' a bien été ajoutée');
        } else {
            return redirect()
                ->route('fiches.index')
                ->with('message', 'La fiche ' . $titre . ' a bien été modifiée');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Fiche $fiche
     * @return array
     */
    public function destroy(Fiche $fiche)
    {
        Manager::remove($fiche);
        Manager::flush();

        return ['success' => true];
    }

    /**
     * Affiche un compte-rendu de toutes les fiches
     * traitées dans l'année.
     *
     * @return \Illuminate\Http\Response
     */
    public function compteRendu()
    {
        $langages = Langage::all();
        $utilisateurs = Utilisateur::all();
        $groupes = [];
        $totalValides = 0;

        foreach ($utilisateurs as $utilisateur) {
            if ( ! $utilisateur->getResponsable()) {
                continue;
            }

            $responsable = $utilisateur->getResponsable();

            if ( ! isset($groupes[$responsable->getId()])) {
                $groupes[$responsable->getId()] = [
                    'responsable' => $responsable->getPrenom() . ' ' . $responsable->getNom(),
                    'eleves' => 0,
                    'fiches' => 0,
                    'moyenne' => 0,
                    'mediane' => 0
                ];
            }

            $rendusValides = count($utilisateur->getRendusValides());
            $totalValides += $rendusValides;

            $groupes[$responsable->getId()]['eleves'] += 1;
            $groupes[$responsable->getId()]['fiches'] += $rendusValides;
            $groupes[$responsable->getId()]['fichesDetail'][] = $rendusValides;
        }

        foreach ($groupes as $id => $groupe) {
            $groupes[$id]['moyenne'] = $groupes[$id]['fiches'] / $groupes[$id]['eleves'];
            $groupes[$id]['mediane'] = $groupes[$id]['fichesDetail'][floor((count($groupes[$id]['fichesDetail']) - 1) / 2)];
        }

        return view('gestion.compte-rendu', compact('langages', 'utilisateurs', 'groupes', 'totalValides'));
    }

    /**
     * Renvoie l'ensemble des fiches disponibles
     * pour afficher dans la liste de suggestions
     * de dépendances.
     *
     * @return array
     */
    public function suggestions()
    {
        $fiches = Fiche::allPositive();
        $suggestions = [];

        foreach ($fiches as $fiche) {
            $suggestions[] = ['text' => $fiche->getTitre(), 'value' => $fiche->getId()];
        }

        return $suggestions;
    }
}
