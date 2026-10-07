<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ReparationController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StatistiqueController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentification (non protégée)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'registerClient'])->middleware('throttle:5,1');

// Routes protégées : authentification obligatoire pour toutes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Lecture : accessible à Admin ET Technicien ET Client (le contrôleur filtre selon le rôle)
    Route::get('/vehicules', [VehiculeController::class, 'index']);
    Route::get('/vehicules/{vehicule}', [VehiculeController::class, 'show']);
    Route::get('/techniciens', [TechnicienController::class, 'index']);
    Route::get('/techniciens/{technicien}', [TechnicienController::class, 'show']);
    Route::get('/reparations', [ReparationController::class, 'index']);
    Route::get('/reparations/{reparation}', [ReparationController::class, 'show']);
    Route::get('/pieces', [PieceController::class, 'index']);
    Route::get('/pieces/{piece}', [PieceController::class, 'show']);
    Route::get('/factures', [FactureController::class, 'index']);
    Route::get('/factures/{facture}', [FactureController::class, 'show']);
    Route::get('/factures/{id}/pdf', [FactureController::class, 'genererPdf']);

    // RDV : accessible à tous les utilisateurs connectés (le contrôleur filtre selon le rôle)
    Route::get('/rendez-vous', [RendezVousController::class, 'index']);
    Route::get('/rendez-vous/{rendezVous}', [RendezVousController::class, 'show']);
    Route::delete('/rendez-vous/{rendezVous}', [RendezVousController::class, 'destroy']);

    // Véhicules : création/modification/suppression accessibles à l'Admin ET au Client
    // (le contrôleur restreint un client à ses propres véhicules uniquement)
    Route::middleware('role:admin,client')->group(function () {
        Route::post('/vehicules', [VehiculeController::class, 'store']);
        Route::put('/vehicules/{vehicule}', [VehiculeController::class, 'update']);
        Route::delete('/vehicules/{vehicule}', [VehiculeController::class, 'destroy']);
    });

    // Écriture : réservée à l'Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/techniciens', [TechnicienController::class, 'store']);
        Route::put('/techniciens/{technicien}', [TechnicienController::class, 'update']);
        Route::delete('/techniciens/{technicien}', [TechnicienController::class, 'destroy']);

        Route::post('/reparations', [ReparationController::class, 'store']);
        Route::delete('/reparations/{reparation}', [ReparationController::class, 'destroy']);

        Route::post('/pieces', [PieceController::class, 'store']);
        Route::put('/pieces/{piece}', [PieceController::class, 'update']);
        Route::delete('/pieces/{piece}', [PieceController::class, 'destroy']);

        Route::post('/factures', [FactureController::class, 'store']);
        Route::put('/factures/{facture}', [FactureController::class, 'update']);
        Route::delete('/factures/{facture}', [FactureController::class, 'destroy']);

        // Confirmation/refus de RDV : réservé à l'admin
        Route::put('/rendez-vous/{rendezVous}', [RendezVousController::class, 'update']);
        Route::get('/clients', [ClientController::class, 'index']);

        Route::get('/statistiques', [StatistiqueController::class, 'index']);
    });

    // Création de RDV : réservée aux clients
    Route::middleware('role:client')->group(function () {
        Route::post('/rendez-vous', [RendezVousController::class, 'store']);
    });

    // Modification d'une réparation : Admin, ou Technicien si assigné
    Route::put('/reparations/{reparation}', [ReparationController::class, 'update']);
    Route::put('/reparations/{reparation}/signaler-terminee', [ReparationController::class, 'signalerTerminee']);
});
