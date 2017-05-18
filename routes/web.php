<?php

use App\Entities\Fiche;
use App\Entities\Rendu;
use App\Entities\Reponse;
use App\Entities\Utilisateur;

use EntityManager as Manager;

# TODO :
# - Sauvegarde/Envoi des réponses (pièces jointes) [x]
# - Préreplir les réponses si jamais sauvegardé avant [x]
# - Statistiques globales sur le compte-rendu [x mais faut récupérer les données années précédentes]
# - Ajout/Suppresion/Modification fiches (avec envoi de fichier, création de langages à la volée, et le système de découpe en exercices) [x sauf synchro Git]
# - Consultation/Téléchargement targz/Validation/Refus des rendus (avec refus en cascade) [x]
# - Ajout/Suppression des utilisateurs (avec envoi de mail de création de compte) [x]
#   -> Seul un administrateur doit avoir un selecteur pour le groupe où va être assigné l'utilisateur,
#      pour les chargés de groupe s'ils ajoutent un utilisateur il ira d'office dans leur groupe. [x]
# - Graphe pour les gestionnaires [x]
# - Mails de synthèse pour les gestionnaires
# - Installer un vrai driver de mails [x]

/**
 * Routes accessibles uniquement aux utilisateurs non authentifiés.
 */
Route::get('/connexion',                          'GuestController@connexion')  ->name('connexion');
Route::post('/connexion',                         'GuestController@connexionPost');
Route::get('/inscription/{utilisateur}/{token}',  'GuestController@inscription')->name('inscription');
Route::post('/inscription/{utilisateur}/{token}', 'GuestController@inscriptionPost');

/**
 * Routes accessibles uniquement aux utilisateurs authentifiés.
 */
Route::get('/',                        'HomeController@index')          ->name('index');
Route::get('/deconnexion',             'HomeController@deconnexion')    ->name('deconnexion');

Route::get('/rendre/{fiche}',          'HomeController@rendre')         ->name('rendre');
Route::post('/rendre/{fiche}',         'HomeController@rendrePost');
Route::get('/piece-jointe/{reponse}',  'HomeController@pieceJointe')    ->name('piece-jointe');
Route::get('/telecharger/{fiche}',     'HomeController@telecharger')    ->name('telecharger');
Route::get('/complementaires/{fiche}', 'HomeController@complementaires')->name('complementaires');

/**
 * Routes de gestion, accessibles aux chargés de groupe et administrateurs seulement.
 */
Route::group([
    'middleware' => ['auth', 'roles.gestion'],
    'prefix' => 'gestion'
], function () {
    Route::post('rendus/{rendu}/valider',    'RendusController@valider')    ->name('rendus.valider');
    Route::post('rendus/{rendu}/refuser',    'RendusController@refuser')    ->name('rendus.refuser');
    Route::get('rendus/{rendu}/telecharger', 'RendusController@telecharger')->name('rendus.telecharger');
    Route::get('rendus/{rendu}',             'RendusController@afficher')   ->name('rendus.afficher');
    Route::get('rendus',                     'RendusController@index')      ->name('rendus.index');

    Route::resource(
        'utilisateurs',
        'UtilisateursController',
        ['parameters' => ['utilisateurs' => 'utilisateur']]
    );
});

/**
 * Routes d'administration, accessibles aux administrateurs seulement.
 */
Route::group([
    'middleware' => ['auth', 'roles.admin'],
    'prefix' => 'administration'
], function () {
    Route::get('compte-rendu',       'FichesController@compteRendu')->name('compte-rendu');

    Route::get('fiches/suggestions', 'FichesController@suggestions')->name('fiches-suggestions');
    Route::resource(
        'fiches',
        'FichesController',
        ['parameters' => ['fiches' => 'fiche']]
    );
});

/**
 * Fonction utilitaires.
 */
// Retrouve une fiche à partir de son identifiant unique
Route::bind('fiche', function ($id) {
    return Manager::find(Fiche::class, (int) $id);
});

// Retrouve un rendu à partir de son identifiant unique
Route::bind('rendu', function ($id) {
    return Manager::find(Rendu::class, (int) $id);
});

// Retrouve une réponse à partir de son identifiant unique
Route::bind('reponse', function ($id) {
    return Manager::find(Reponse::class, (int) $id);
});

// Retrouve un utilisateur à partir de son identifiant unique
Route::bind('utilisateur', function ($id) {
    return Manager::find(Utilisateur::class, (int) $id);
});
