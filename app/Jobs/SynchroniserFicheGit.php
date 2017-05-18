<?php

namespace App\Jobs;

use App\Entities\Fiche;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SynchroniserFicheGit implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * La fiche que l'on souhaite synchroniser
     * avec le serveur Git.
     *
     * @var Fiche
     */
    protected $fiche;

    /**
     * Initialise la tâche.
     *
     * @param Fiche $fiche
     */
    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
    }

    /**
     * Synchronise la fiche avec le serveur Git.
     *
     * @return void
     */
    public function handle()
    {
        $chemin = $this->fiche->getCheminGit();

        # TODO : Télécharger depuis Git
    }
}
