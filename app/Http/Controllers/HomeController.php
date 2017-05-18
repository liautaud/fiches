<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Entities\Fiche;
use App\Entities\Rendu;
use App\Entities\Utilisateur;
use App\Entities\Reponse;
use App\Entities\Exercice;

use Uploadable;
use EntityManager as Manager;

class HomeController extends Controller
{
    /**
     * Instancie le controlleur, et s'assure que seuls les
     * utilisateurs authentifiés y ont accès.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche le graphe des fiches disponibles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $utilisateur = $request->user();

        if ($utilisateur->isGestionnaire()) {
            $graphe = $utilisateur->getGrapheGestionnaire();
        } else {
            $graphe = $utilisateur->getGrapheUtilisateur();
        }

        return view('index', compact('utilisateur', 'graphe'));
    }

    /**
     * Affiche l'interface de rendu d'une fiche.
     *
     * @return \Illuminate\Http\Response
     */
    public function rendre(Request $request, Fiche $fiche)
    {
        $utilisateur = $request->user();

        if ($fiche->getId() == 0 || ! $utilisateur->canAccess($fiche)){
            return abort(401);
        }

        $peutSoumettre = $utilisateur->canSubmit($fiche);
        $afficherNomPrenom = false;
        $dernierRendu = $utilisateur->getDernierRenduForFiche($fiche);

        return view('rendre', compact('fiche', 'peutSoumettre', 'afficherNomPrenom', 'dernierRendu'));
    }

    /**
     * Soumet un rendu de fiche en base de données.
     *
     * @return \Illuminate\Http\Response
     */
    public function rendrePost(Request $request, Fiche $fiche)
    {
        $utilisateur = $request->user();
        $dernierRendu = $utilisateur->getDernierRenduForFiche($fiche);

        if ( ! $utilisateur->canSubmit($fiche)){
            return abort(401);
        }

        if (empty($request->input('reponses')) ||
            ! is_array($request->input('reponses')) ||
            count($request->input('reponses')) < count($fiche->getExercices())) {
            return abort(403);
        }

        if ( ! $request->hasFile('fichiers')) {
            $fichiers = [];
        } else {
            $fichiers = $request->file('fichiers');
        }

        if ( ! is_null($request->input('validation'))) {
            $etat = Rendu::ETAT_SOUMIS;
        } else {
            $etat = Rendu::ETAT_BROUILLON;
        }

        $rendu = new Rendu;
        $rendu->setEtat($etat);
        $rendu->setFiche($fiche);
        $rendu->setUtilisateur($utilisateur);

        Manager::persist($rendu);
        $reponses = [];

        foreach ($request->input('reponses') as $numero => $contenu) {
            $exercice = $fiche->getExercice((int) $numero);
            
            if (is_null($exercice)) {
                return abort(403);
            }

            $reponse = new Reponse;
            $reponse->setRendu($rendu);
            $reponse->setExercice($exercice);
            $reponse->setNumero($exercice->getNumero());

            $reponseVide = true;

            // On stocke la réponse entrée dans l'éditeur de
            // texte si elle n'est pas vide.
            if (!empty($contenu)) {
                $reponseVide = false;
                $reponse->setContenu($contenu);
            }

            // On stocke la pièce jointe qui vient d'être envoyée,
            // ou a défaut celle envoyée avec un rendu précédent.
            if (isset($fichiers[$numero])) {
                $reponseVide = false;
                $fichier = $fichiers[$numero];
                Uploadable::addEntityFileInfo($reponse, [
                    'tmp_name' => $fichier->getRealPath(),
                    'name' => $fichier->getClientOriginalName(),
                    'size' => $fichier->getSize(),
                    'type' => $fichier->getType(),
                    'error' => $fichier->getError()
                ]);
            } else if ($dernierRendu &&
                $dernierRendu->getReponse($numero) &&
                $dernierRendu->getReponse($numero)->getChemin()) {
                // Peut-être y avait t'il déjà une pièce jointe dans un rendu
                // précédent, auquel cas on la conserve.
                $reponseVide = false;
                $reponse->setChemin($dernierRendu->getReponse($numero)->getChemin());
            } 
            
            if ($etat == Rendu::ETAT_SOUMIS && $reponseVide) {
                // Dans le cas où l'utilisateur n'a répondu à une question ni par
                // du texte, ni par une pièce jointe, ni en laissant inchangée
                // la pièce jointe d'une sauvegarde précédente, alors on refuse
                // la validation de la fiche.
                return redirect()
                    ->route('rendre', $fiche->getId())
                    ->with('message', 'Toutes les réponses doivent être remplies !');
            }

            Manager::persist($reponse);
            $reponses[] = $reponse;    
        }

        Manager::flush();

        $verb = ($etat == Rendu::ETAT_BROUILLON) ? 'sauvegardées' : 'envoyées';

        return redirect()
            ->route('index')
            ->with('message', 'Les réponses ont bien été ' . $verb . ' !');
    }

    /**
     * Envoie le fichier d'une fiche à l'utilisateur.
     *
     * @return \Illuminate\Http\Response
     */
    public function telecharger(Request $request, Fiche $fiche)
    {
        $utilisateur = $request->user();

        if ( ! $utilisateur->canAccess($fiche)){
            return abort(401);
        }

        return response()->file(base_path() . '/git/' . $fiche->getCheminGit());
    }

    /**
     * Envoie les fichiers complémentaires d'une fiche à l'utilisateur.
     *
     * @return \Illuminate\Http\Response
     */
    public function complementaires(Request $request, Fiche $fiche)
    {
        $utilisateur = $request->user();

        if ( ! $utilisateur->canAccess($fiche)){
            return abort(401);
        }

        return response()->file(base_path() . '/git/' . $fiche->getCheminGitComplementaires());
    }

    /**
     * Affiche la pièce-jointe liée à une réponse.
     *
     * @return \Illuminate\Http\Response
     */
    public function pieceJointe(Reponse $reponse)
    {
        if ( ! $reponse->getId()) {
            return abort(404);
        }

        if ( ! $reponse->getChemin()) {
            return abort(404);
        }

        return response()->download($reponse->getChemin());
    }

    /** 
     * Déconnecte l'utilisateur courant.
     *
     * @return \Illuminate\Http\Response
     */
    public function deconnexion()
    {
        auth()->logout();

        return redirect()->route('connexion');
    }
}
