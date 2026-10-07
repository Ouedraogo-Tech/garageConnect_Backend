<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FactureGenereeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Facture $facture;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    public function build()
    {
        return $this->subject('Votre facture ' . $this->facture->numero . ' — GarageConnect')
            ->view('emails.facture');
    }
}
