<?php

namespace App\Http\Controllers;

use App\Jobs\EnvoyerEmailInscription;
use LaravelDoctrine\ORM\Facades\EntityManager as Manager;
use Doctrine\Common\Collections\Criteria;

use App\Entities\Utilisateur;
use Illuminate\Http\Request;

class UtilisateursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $utilisateurCourant = $request->user();

        // Si l'utilisateur courant est un administrateur, on lui donne
        // accès à tous les profils utilisateurs. Si, par contre, c'est
        // "seulement" un chargé de groupe, on ne lui donne accès qu'à
        // ses élèves.
        if ($utilisateurCourant->isAdministrateur()) {
            $utilisateurs = Utilisateur::all();
        } else {
            $utilisateurs = $utilisateurCourant->getEleves();
        }

        return view('gestion.utilisateurs.index', compact('utilisateurs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit(new Utilisateur(), true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Utilisateur(), true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Utilisateur  $utilisateur
     * @param  bool $ajout
     * @return \Illuminate\Http\Response
     */
    public function edit(Utilisateur $courant, $ajout = false)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('statut', 1));

        $responsables = Manager::getRepository(Utilisateur::class)
            ->matching($criteria);

        return view('gestion.utilisateurs.edit', compact('courant', 'responsables', 'ajout'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Utilisateur $utilisateur
     * @param  bool $ajout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Utilisateur $utilisateur, $ajout = false)
    {
        $this->validate($request, [
            'nom'         => 'required|max:255',
            'prenom'      => 'required|max:255',
            'email'       => 'required|email|max:255',
            'statut'      => 'required|integer',
            'responsable' => 'required|integer',
        ]);

        $utilisateur->setNom($request->input('nom'));
        $utilisateur->setPrenom($request->input('prenom'));
        $utilisateur->setEmail($request->input('email'));
        $utilisateur->setStatut((int) $request->input('statut'));
        $utilisateur->setPromotion((int) $request->input('promotion'));

        if ($request->input('responsable') == 0) {
            $utilisateur->setResponsable(null);
        } else {
            $responsable = Manager::find(Utilisateur::class, (int)$request->input('responsable'));

            if (!$responsable) {
                return abort(403);
            }

            $utilisateur->setResponsable($responsable);
        }

        Manager::persist($utilisateur);
        Manager::flush();

        if ($ajout) {
            // Dans le cas où on vient de créer l'utilisateur,
            // on lui envoie un e-mail l'invitant à finaliser
            // son inscription en choisissant un mot de passe.
            $this->dispatch(new EnvoyerEmailInscription($utilisateur));

            return redirect()
                ->route('utilisateurs.index')
                ->with('message', 'L\'utilisateur ' . $request->input('email') . ' a bien été ajouté !');
        } else {
            return redirect()
                ->route('utilisateurs.index')
                ->with('message', 'L\'utilisateur ' . $request->input('email') . ' a bien été modifié !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Utilisateur $utilisateur
     * @return array
     */
    public function destroy(Utilisateur $utilisateur)
    {
        Manager::remove($utilisateur);
        Manager::flush();

        return ['success' => true];
    }
}
