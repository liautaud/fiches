<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Entities\Fiche;
use App\Entities\Rendu;
use App\Entities\Utilisateur;
use App\Entities\Reponse;
use App\Entities\Exercice;

use EntityManager as Manager;

class GuestController extends Controller
{
    /**
     * Instancie le controlleur, et s'assure que seuls les
     * utilisateurs non authentifiés y ont accès.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('roles.guest');
    }

    /**
     * Affiche le formulaire de connexion.
     *
     * @return \Illuminate\Http\Response
     */
    public function connexion(Request $request)
    {
        return view('connexion');
    }

    /**
     * Essaye de connecter l'utilisateur.
     *
     * @return \Illuminate\Http\Response
     */
    public function connexionPost(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (auth()->attempt(['email' => $email, 'password' => $password], true)) {
            return redirect()->route('index');
        } else {
            return redirect()
                ->route('connexion')
                ->with('message', 'L\'adresse e-mail ou le mot de passe sont incorrects !');
        }
    }
    
    /**
     * Propose à l'utilisateur de terminer son
     * inscription en choisissant son mot de
     * passe.
     *
     * @return \Illuminate\Http\Response
     */
    public function inscription(Utilisateur $utilisateur, $token)
    {
    	if ( ! $utilisateur) {
    		return abort(404);
    	}
    	
    	if (is_null($utilisateur->getInscriptionToken()) ||
    		$utilisateur->getInscriptionToken() != $token) {
    		return abort(401);
    	}
    	
    	return view('inscription', compact('utilisateur'));
    }
    
    /**
     * Essaye de terminer l'inscription d'un
     * utilisateur en enregistrant son mot
     * de passe.
     *
     * @return \Illuminate\Http\Response
     */
    public function inscriptionPost(Request $request, Utilisateur $utilisateur, $token)
    {
    	if ( ! $utilisateur) {
    		return abort(404);
    	}
    	                            
    	if (is_null($utilisateur->getInscriptionToken()) ||
    	    $utilisateur->getInscriptionToken() != $token) {
    	    return abort(401);
    	}
    	
    	if ( ! $request->has('password') ||
    		 ! $request->has('password-confirm') ||
    		$request->input('password') != $request->input('password-confirm')) {
    		return redirect()
    			->route('inscription', [$utilisateur->getId(), $token])
    			->with('message', 'Les champs ne correspondent pas !');
    	}
    	
    	$utilisateur->setPassword(bcrypt($request->input('password')));
    	$utilisateur->removeInscriptionToken();
    	
    	Manager::persist($utilisateur);
    	Manager::flush();
    	
    	return redirect()
    		->route('connexion')
    		->with('message', 'Votre inscription est terminée !');
	}
}
