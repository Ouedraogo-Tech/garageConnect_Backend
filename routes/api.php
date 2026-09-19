<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ReparationController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentification (non protégée)
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées : authentification obligatoire pour toutes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Lecture : accessible à Admin ET Technicien
    Route::get('/vehicules', [VehiculeController::class, 'index']);
    Route::get('/vehicules/{vehicule}', [VehiculeController::class, 'show']);
    Route::get('/techniciens', [TechnicienController::class, 'index']);
    Route::get('/techniciens/{technicien}', [TechnicienController::class, 'show']);
    Route::get('/reparations', [ReparationController::class, 'index']);
    Route::get('/reparations/{reparation}', [ReparationController::class, 'show']);

    // Écriture : réservée à l'Admin
    Route::middleware('role:admin')->group(function () {
        Route::post('/vehicules', [VehiculeController::class, 'store']);
        Route::put('/vehicules/{vehicule}', [VehiculeController::class, 'update']);
        Route::delete('/vehicules/{vehicule}', [VehiculeController::class, 'destroy']);
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/techniciens', [TechnicienController::class, 'store']);
        Route::put('/techniciens/{technicien}', [TechnicienController::class, 'update']);
        Route::delete('/techniciens/{technicien}', [TechnicienController::class, 'destroy']);

        Route::post('/reparations', [ReparationController::class, 'store']);
        Route::delete('/reparations/{reparation}', [ReparationController::class, 'destroy']);
    });

    // Modification d'une réparation : Admin, ou Technicien si assigné
    Route::put('/reparations/{reparation}', [ReparationController::class, 'update']);
});
