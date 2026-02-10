<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VoitureController;
use App\Http\Controllers\Api\ReparationController;
use App\Http\Controllers\Api\InterventionController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TypeInterventionController; // Add import
use App\Http\Controllers\Api\StatistiqueController;

/* Route::get('/test', [TestController::class, 'test']);
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Routes publiques
    Route::get('/test', [TestController::class, 'test']);
    // Route::get('/test/types', [TestController::class, 'types']);
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/types-intervention', [InterventionController::class, 'types']); // Keep for backward compatibility
    Route::get('/statistiques', [StatistiqueController::class, 'public']);

    // Routes pour le backoffice web (pas auth:sanctum car le web utilise un token simple)
    Route::get('voitures', [VoitureController::class, 'index']);
    Route::post('voitures', [VoitureController::class, 'store']);
    Route::get('voitures/{voiture}', [VoitureController::class, 'show']);

    Route::get('reparations', [ReparationController::class, 'index']);
    Route::post('reparations', [ReparationController::class, 'store']);
    Route::get('reparations/en-cours', [ReparationController::class, 'enCours']);
    Route::get('reparations/terminees', [ReparationController::class, 'terminees']);
    Route::post('reparations/{reparation}/commencer', [ReparationController::class, 'commencer']);
    Route::put('reparations/{reparation}/progression', [ReparationController::class, 'updateProgression']);
    Route::post('reparations/{reparation}/terminer', [ReparationController::class, 'terminer']);
    Route::get('reparations/slots', [ReparationController::class, 'slotsDisponibles']);

    Route::get('interventions', [InterventionController::class, 'index']);
    Route::post('voitures/{voiture}/interventions', [InterventionController::class, 'storeForVoiture']);

    // Routes protégées (app mobile avec Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);

        Route::get('voitures/{id}/reparations', [VoitureController::class, 'reparations']);

        Route::apiResource('types-intervention-manage', TypeInterventionController::class); // New CRUD route

        Route::apiResource('commandes', CommandeController::class);
        Route::post('commandes/{commande}/paiement', [CommandeController::class, 'processPaiement']);
        Route::get('commandes/{commande}/facture', [CommandeController::class, 'facture']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/non-lues', [NotificationController::class, 'nonLues']);
        Route::put('notifications/{notification}/marquer-lue', [NotificationController::class, 'marquerLue']);
        Route::put('notifications/marquer-toutes-lues', [NotificationController::class, 'marquerToutesLues']);

        Route::middleware('admin')->group(function () {
            Route::get('admin/statistiques', [StatistiqueController::class, 'admin']);
            Route::get('admin/rapports', [StatistiqueController::class, 'rapports']);
        });
    });
});