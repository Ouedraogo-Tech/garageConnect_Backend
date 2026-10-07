<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #0d6efd; }
        .header p { margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f1f1f1; }
        .totaux { margin-top: 15px; width: 50%; margin-left: auto; }
        .totaux td { border: none; padding: 4px 8px; }
        .totaux .total { font-weight: bold; font-size: 15px; border-top: 2px solid #333; }
        .statut { display: inline-block; padding: 4px 10px; border-radius: 4px; color: #fff; font-weight: bold; }
        .statut.payee { background-color: #198754; }
        .statut.impayee { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
    <img src="{{ public_path('images/atelio-wordmark-pdf.png') }}" style="height: 50px;">
    <p>Facture n° {{ $facture->numero }}</p>
</div>

    <table style="border: none; margin-bottom: 10px;">
        <tr style="border: none;">
            <td style="border: none;">
                <strong>Client :</strong><br>
                {{ $facture->reparation->vehicule->client->prenom ?? '' }} {{ $facture->reparation->vehicule->client->nom ?? '' }}<br>
                {{ $facture->reparation->vehicule->client->telephone ?? '' }}
            </td>
            <td style="border: none; text-align: right;">
                <strong>Date d'émission :</strong> {{ $facture->date_emission }}<br>
                <strong>Statut :</strong>
                <span class="statut {{ $facture->statut }}">
                    {{ $facture->statut === 'payee' ? 'Payée' : 'Impayée' }}
                </span>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Véhicule</th>
            <th>Objet de la réparation</th>
            <th>Date</th>
        </tr>
        <tr>
            <td>{{ $facture->reparation->vehicule->marque }} {{ $facture->reparation->vehicule->modele }} ({{ $facture->reparation->vehicule->immatriculation }})</td>
            <td>{{ $facture->reparation->objet_reparation }}</td>
            <td>{{ $facture->reparation->date }}</td>
        </tr>
    </table>

    @if ($facture->reparation->pieces->count())
    <table>
        <tr>
            <th>Pièce</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Sous-total</th>
        </tr>
        @foreach ($facture->reparation->pieces as $piece)
        <tr>
            <td>{{ $piece->nom }}</td>
            <td>{{ $piece->pivot->quantite_utilisee }}</td>
            <td>{{ number_format($piece->prix_unitaire, 0, ',', ' ') }} FCFA</td>
            <td>{{ number_format($piece->pivot->quantite_utilisee * $piece->prix_unitaire, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
    </table>
    @endif

    <table class="totaux">
        <tr>
            <td>Main d'œuvre</td>
            <td style="text-align: right;">{{ number_format($facture->montant_main_oeuvre, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>Pièces</td>
            <td style="text-align: right;">{{ number_format($facture->montant_pieces, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total">
            <td>Total</td>
            <td style="text-align: right;">{{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>
</body>
</html>
