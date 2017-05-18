<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Entities\Utilisateur;

class Inscription extends Mailable
{
    /**
     * Le destinataire du message.
     */
    public $destinataire;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Utilisateur $destinataire)
    {
        $this->destinataire = $destinataire;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.inscription');
    }
}
