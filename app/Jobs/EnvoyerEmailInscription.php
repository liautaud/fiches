<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Entities\Utilisateur;
use App\Mail\Inscription;

use Illuminate\Support\Facades\Mail;
use LaravelDoctrine\ORM\Facades\EntityManager as Manager;

class EnvoyerEmailInscription
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // On commence par générer un token d'inscription
        // aléatoire pour l'utilisateur.
        $this->utilisateur->generateInscriptionToken();

        Manager::persist($this->utilisateur);
        Manager::flush();

        // On envoie ensuite un email à l'utilisateur avec
        // un lien pour définir son mot de passe.
        Mail::to($this->utilisateur->getEmail())
            ->send(new Inscription($this->utilisateur));
    }
}
