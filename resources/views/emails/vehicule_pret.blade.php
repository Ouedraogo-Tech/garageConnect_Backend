<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Atelio</h2>
    <p>Bonjour,</p>
    <p>Bonne nouvelle : la réparation de votre véhicule est terminée et il est prêt à être récupéré.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 500px;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Véhicule</td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                {{ $reparation->vehicule->marque }} {{ $reparation->vehicule->modele }}
                ({{ $reparation->vehicule->immatriculation }})
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Objet de la réparation</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $reparation->objet_reparation }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Date de fin</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $reparation->date_fin_reelle }}</td>
        </tr>
    </table>

    <p style="margin-top: 20px;">Vous pouvez venir récupérer votre véhicule au garage dès maintenant.</p>
    <p>Merci de votre confiance.<br>L'équipe Atelio</p>
</body>
</html>
