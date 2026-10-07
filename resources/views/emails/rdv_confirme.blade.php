<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Atelio</h2>
    <p>Bonjour,</p>
    <p>Votre rendez-vous du <strong>{{ $rendezVous->date_rdv }}</strong> à <strong>{{ substr($rendezVous->heure_rdv, 0, 5) }}</strong> a été confirmé.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 500px;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Véhicule</td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                {{ $rendezVous->vehicule->marque }} {{ $rendezVous->vehicule->modele }}
                ({{ $rendezVous->vehicule->immatriculation }})
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Motif</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $rendezVous->motif }}</td>
        </tr>
        @if($dateFinPrevue)
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Date de fin estimée (indicative)</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $dateFinPrevue }}</td>
        </tr>
        @endif
    </table>

    <p style="margin-top: 20px;">
        Cette date est donnée à titre indicatif et peut varier selon l'avancement des travaux.
        Vous recevrez un nouveau message dès que votre véhicule sera prêt à être récupéré.
    </p>
    <p>Merci de votre confiance.<br>L'équipe Atelio</p>
</body>
</html>
