<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Piece;
use App\Models\Reparation;
use App\Models\RendezVous;
use App\Models\Technicien;
use App\Models\Vehicule;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index()
    {
        $topTechniciens = Technicien::withCount('reparations')
            ->orderByDesc('reparations_count')
            ->take(5)
            ->get(['id', 'nom', 'prenom']);

        return response()->json([
            'vehicules_total' => Vehicule::count(),
            'clients_total' => Client::count(),
            'techniciens_total' => Technicien::count(),

            'reparations_total' => Reparation::count(),
            'reparations_mois' => Reparation::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),

            'factures_total' => Facture::count(),
            'chiffre_affaires' => (float) Facture::where('statut', 'payee')->sum('montant_total'),
            'chiffre_affaires_mois' => (float) Facture::where('statut', 'payee')
                ->whereMonth('date_emission', now()->month)
                ->whereYear('date_emission', now()->year)
                ->sum('montant_total'),
            'factures_impayees_nombre' => Facture::where('statut', 'impayee')->count(),
            'factures_impayees_montant' => (float) Facture::where('statut', 'impayee')->sum('montant_total'),

            'pieces_en_alerte' => Piece::whereColumn('quantite_stock', '<=', 'seuil_alerte')->count(),

            'rdv_en_attente' => RendezVous::where('statut', 'en_attente')->count(),
            'rdv_confirmes' => RendezVous::where('statut', 'confirme')->count(),
            'taux_confirmation_rdv' => $this->tauxConfirmationRdv(),

            'delai_moyen_jours' => $this->delaiMoyenReparation(),
            'repartition_reparations' => $this->repartitionReparations(),
            'ca_par_mois' => $this->caParMois(),

            'top_techniciens' => $topTechniciens,
        ]);
    }

    /**
     * % de RDV confirmés parmi ceux déjà traités (confirmés + refusés).
     * Les RDV encore "en_attente" ne comptent pas dans le taux.
     */
    private function tauxConfirmationRdv(): ?float
    {
        $confirmes = RendezVous::where('statut', 'confirme')->count();
        $refuses = RendezVous::where('statut', 'refuse')->count();
        $traites = $confirmes + $refuses;

        return $traites > 0 ? round($confirmes / $traites * 100, 1) : null;
    }

    /**
     * Délai moyen (en jours) entre le début et la fin réelle des réparations terminées.
     * Calculé en PHP (pas en SQL) pour rester portable entre MySQL et SQLite (tests).
     */
    private function delaiMoyenReparation(): ?float
    {
        $reparations = Reparation::whereNotNull('date_fin_reelle')->get(['date', 'date_fin_reelle']);

        if ($reparations->isEmpty()) {
            return null;
        }

        $totalJours = $reparations->sum(function ($r) {
            return Carbon::parse($r->date)->diffInDays(Carbon::parse($r->date_fin_reelle));
        });

        return round($totalJours / $reparations->count(), 1);
    }

    /**
     * Répartition des réparations par statut dérivé (pas une colonne en base).
     */
    private function repartitionReparations(): array
    {
        return [
            'en_cours' => Reparation::whereNull('date_fin_reelle')->whereNull('signalee_terminee_le')->count(),
            'signalee' => Reparation::whereNotNull('signalee_terminee_le')->whereNull('date_fin_reelle')->count(),
            'terminee' => Reparation::whereNotNull('date_fin_reelle')->count(),
        ];
    }

    /**
     * Chiffre d'affaires encaissé sur les 6 derniers mois (mois courant inclus).
     */
    private function caParMois(): array
    {
        $resultat = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $montant = (float) Facture::where('statut', 'payee')
                ->whereMonth('date_emission', $date->month)
                ->whereYear('date_emission', $date->year)
                ->sum('montant_total');

            $resultat[] = [
                'mois' => $date->translatedFormat('M Y'),
                'montant' => $montant,
            ];
        }

        return $resultat;
    }
}
