<?php

namespace App\Mail;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RdvConfirmeMail extends Mailable
{
    use Queueable, SerializesModels;

    public RendezVous $rendezVous;
    public ?string $dateFinPrevue;

    public function __construct(RendezVous $rendezVous, ?string $dateFinPrevue = null)
    {
        $this->rendezVous = $rendezVous;
        $this->dateFinPrevue = $dateFinPrevue;
    }

    public function build()
    {
        return $this->subject('Votre rendez-vous est confirmé — GarageConnect')
            ->view('emails.rdv_confirme');
    }
}
