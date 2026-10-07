<?php

namespace App\Mail;

use App\Models\Reparation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VehiculePretMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reparation $reparation;

    public function __construct(Reparation $reparation)
    {
        $this->reparation = $reparation;
    }

    public function build()
    {
        return $this->subject('Votre véhicule est prêt — GarageConnect')
            ->view('emails.vehicule_pret');
    }
}
