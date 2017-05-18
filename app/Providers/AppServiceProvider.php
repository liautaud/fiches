<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_TIME, 'fr_FR.utf8');

        view()->composer('*', function ($view) {
            $utilisateur = auth()->user();

            if ($utilisateur) {
                $view->with('utilisateur', $utilisateur);

                $statistiques = ($utilisateur->isGestionnaire()) ?
                    $utilisateur->getStatistiquesGestionnaire() :
                    $utilisateur->getStatistiquesUtilisateur();
                
                $view->with('statistiques', $statistiques);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
