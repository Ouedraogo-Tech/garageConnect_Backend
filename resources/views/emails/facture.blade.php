<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Atelio</h2>
    <p>Bonjour,</p>
    <p>Votre facture <strong>{{ $facture->numero }}</strong> a été générée le {{ $facture->date_emission }}.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 500px;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Véhicule</td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                {{ $facture->reparation->vehicule->marque }} {{ $facture->reparation->vehicule->modele }}
                ({{ $facture->reparation->vehicule->immatriculation }})
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Objet</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $facture->reparation->objet_reparation }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Main d'œuvre</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format($facture->montant_main_oeuvre, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Pièces</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format($facture->montant_pieces, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="padding: 8px; border: 1px solid #ddd;">Total</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <p style="margin-top: 20px;">Merci de votre confiance.<br>L'équipe Atelio</p>
</body>
</html>
