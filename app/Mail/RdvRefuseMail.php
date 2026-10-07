<?php

namespace App\Mail;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RdvRefuseMail extends Mailable
{
    use Queueable, SerializesModels;

    public RendezVous $rendezVous;

    public function __construct(RendezVous $rendezVous)
    {
        $this->rendezVous = $rendezVous;
    }

    public function build()
    {
        return $this->subject("Votre demande de rendez-vous n'a pas pu être acceptée — GarageConnect")
            ->view('emails.rdv_refuse');
    }
}
