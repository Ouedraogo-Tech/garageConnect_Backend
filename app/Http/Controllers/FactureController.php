<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\UpdateFactureRequest;
use App\Models\Facture;
use App\Models\Reparation;
use Illuminate\Http\Request;
use App\Mail\FactureGenereeMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    private const TAUX_HORAIRE = 5000; // FCFA par heure de main d'œuvre

    public function index(Request $request)
    {
        $query = Facture::with('reparation.vehicule');

        if ($request->filled('recherche')) {
            $query->where('numero', 'like', "%{$request->input('recherche')}%");
        }

        return $query->orderByDesc('date_emission')->paginate(10);
    }

    /**
     * Génère une facture à partir d'une réparation existante.
     * Calcule automatiquement les montants et décrémente le stock des pièces utilisées.
     */
    public function store(StoreFactureRequest $request)
    {
        $reparation = Reparation::with('pieces')->findOrFail($request->reparation_id);

        $montantMainOeuvre = $reparation->duree_main_oeuvre * self::TAUX_HORAIRE;

        $montantPieces = 0;
        foreach ($reparation->pieces as $piece) {
            $montantPieces += $piece->pivot->quantite_utilisee * $piece->prix_unitaire;

            // Décrémente le stock au moment de la facturation
            $piece->decrement('quantite_stock', $piece->pivot->quantite_utilisee);
        }

        $facture = Facture::create([
            'reparation_id' => $reparation->id,
            'numero' => 'FAC-' . date('Ymd') . '-' . str_pad($reparation->id, 4, '0', STR_PAD_LEFT),
            'montant_main_oeuvre' => $montantMainOeuvre,
            'montant_pieces' => $montantPieces,
            'montant_total' => $montantMainOeuvre + $montantPieces,
            'statut' => 'impayee',
            'date_emission' => now()->toDateString(),
        ]);

        if ($reparation->vehicule->email_proprietaire) {
            Mail::to($reparation->vehicule->email_proprietaire)->send(new FactureGenereeMail($facture));
        }

        return response()->json($facture->load('reparation.vehicule'), 201);
    }

   public function show(Request $request, $id)
{
    $facture = Facture::with('reparation.vehicule', 'reparation.techniciens', 'reparation.pieces')->findOrFail($id);

    if (! $request->user()->can('view', $facture)) {
        return response()->json(['message' => 'Accès refusé.'], 403);
    }

    return response()->json($facture);
}
    /**
     * Change uniquement le statut (payée / impayée).
     */
    public function update(UpdateFactureRequest $request, $id)
    {
        $facture = Facture::findOrFail($id);
        $facture->update(['statut' => $request->statut]);
        return response()->json($facture);
    }

    public function destroy($id)
    {
        Facture::destroy($id);
        return response()->json(null, 204);
    }

    /**
     * Génère un PDF téléchargeable pour une facture.
     * Admin : accès à toutes les factures. Client : uniquement la sienne.
     */
   public function genererPdf(Request $request, $id)
{
    $facture = Facture::with('reparation.vehicule.client.user', 'reparation.techniciens', 'reparation.pieces')
        ->findOrFail($id);

    if (! $request->user()->can('view', $facture)) {
        return response()->json(['message' => 'Accès refusé.'], 403);
    }

    $pdf = Pdf::loadView('factures.pdf', ['facture' => $facture]);

    return $pdf->download("{$facture->numero}.pdf");
}
}
