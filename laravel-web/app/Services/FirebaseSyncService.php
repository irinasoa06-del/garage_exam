<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseSyncService
{
    protected $projectId;
    protected $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'garage-exam');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Synchroniser une réparation vers Firestore via REST API
     * Collection: clients (pour compatibilité avec le frontend)
     */
    public function syncReparation($reparation)
    {
        try {
            // Préparer les données au format Firestore REST API
            $data = [
                'fields' => [
                    'nom' => ['stringValue' => $reparation->voiture->user->nom . ' ' . $reparation->voiture->user->prenom],
                    'voiture' => ['stringValue' => $reparation->voiture->marque . ' ' . $reparation->voiture->modele],
                    'immatriculation' => ['stringValue' => $reparation->voiture->immatriculation ?? ''],
                    'typeIntervention' => ['stringValue' => $reparation->type->nom],
                    'statut' => ['stringValue' => $reparation->statut],
                    'prix' => ['doubleValue' => (float)$reparation->type->prix_unitaire],
                    'progression' => ['integerValue' => (int)$reparation->progression],
                    'reparation_id' => ['integerValue' => (int)$reparation->reparation_id],
                    'updated_at' => ['stringValue' => now()->toIso8601String()]
                ]
            ];

            // Document ID = reparation_id pour éviter les doublons
            $documentId = (string)$reparation->reparation_id;
            $url = "{$this->baseUrl}/clients/{$documentId}";

            // PATCH avec ?updateMask pour merge (ou créer si n'existe pas)
            $response = Http::withoutVerifying()
                ->patch($url . '?updateMask.fieldPaths=*', $data);

            if (!$response->successful()) {
                // Si PATCH échoue (document n'existe pas), créer avec PUT
                $response = Http::withoutVerifying()
                    ->put($url, $data);
            }

            if (!$response->successful()) {
                Log::warning("Firebase REST sync failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::warning("Erreur Sync Firebase REST: " . $e->getMessage());
        }
    }

    /**
     * Supprimer une réparation de Firestore
     */
    public function removeReparation($id)
    {
        try {
            $documentId = (string)$id;
            $url = "{$this->baseUrl}/clients/{$documentId}";

            $response = Http::withoutVerifying()->delete($url);

            if (!$response->successful()) {
                Log::warning("Firebase REST delete failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::warning("Erreur suppression Firebase REST: " . $e->getMessage());
        }
    }
}
