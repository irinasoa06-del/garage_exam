<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReparationEnCours;
use App\Models\Voiture;
use App\Models\TypeIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Import Log facade
use Carbon\Carbon;

use App\Services\FirebaseSyncService;

class ReparationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseSyncService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    /**
     * Liste des réparations (toutes)
     */
    public function index(Request $request) {
        $reparations = ReparationEnCours::with(['voiture', 'type', 'technicien'])
            ->orderBy('date_creation', 'desc')
            ->get();
        return response()->json(['reparations' => $reparations]);
    }

    /**
     * Liste des réparations en cours
     */
    public function enCours(Request $request) {
        $reparations = ReparationEnCours::with(['voiture.user', 'type']) // Include user
            ->whereIn('statut', ['en_cours', 'en_attente']) // Inclure en attente de slot
            ->get();
        return response()->json(['reparations' => $reparations]);
    }

    /**
     * Liste des réparations terminées
     */
    public function terminees(Request $request) {
        $reparations = ReparationEnCours::with(['voiture', 'type'])
            ->where('statut', 'terminee')
            ->orderBy('date_modification', 'desc')
            ->limit(50)
            ->get();
        return response()->json(['reparations' => $reparations]);
    }

    /**
     * Créer une nouvelle réparation (depuis le backoffice)
     */
    public function store(Request $request) {
        // Debug log to see what we're receiving
        Log::info('Creating reparation START', $request->all());

        $validator = Validator::make($request->all(), [
            'voiture_id' => 'required', // Relaxed from integer to just required
            'type_intervention_id' => 'required',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed EXACT', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Données invalides',
                'details' => $validator->errors()
            ], 422);
        }

        // Vérifier que la voiture existe
        $voiture = Voiture::find($request->voiture_id);
        if (!$voiture) {
            return response()->json([
                'message' => 'Voiture introuvable',
                'errors' => ['voiture_id' => ['Cette voiture n\'existe pas']]
            ], 422);
        }

        // Vérifier que le type existe
        $type = TypeIntervention::find($request->type_intervention_id);
        if (!$type) {
            return response()->json([
                'message' => 'Type d\'intervention introuvable',
                'errors' => ['type_intervention_id' => ['Ce type d\'intervention n\'existe pas']]
            ], 422);
        }

        try {
            $reparation = ReparationEnCours::create([
                'voiture_id' => $request->voiture_id,
                'type_id' => $request->type_intervention_id,
                'statut' => 'en_attente',
                'progression' => 0,
                'date_debut' => null,
                'slot_garage' => null
            ]);

            $reparation->load(['voiture.user', 'type']);
            
            // Sync avec Firebase
            try {
                $this->firebaseService->syncReparation($reparation);
            } catch (\Exception $e) {
                Log::warning('Firebase sync failed but reparation created', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'message' => 'Réparation créée avec succès',
                'reparation' => $reparation
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create reparation', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'errors' => []
            ], 500);
        }
    }

    /**
     * Commencer une réparation (Assigner un slot)
     */
    public function commencer(Request $request, $id) {
        $reparation = ReparationEnCours::find($id);
        if (!$reparation) return response()->json(['message' => 'Non trouvé'], 404);

        if ($reparation->statut !== 'en_attente') {
            return response()->json(['message' => 'Réparation déjà commencée ou terminée'], 400);
        }

        // Vérifier slots disponibles (Max 2)
        $slotsOccupes = ReparationEnCours::where('statut', 'en_cours')->count();
        if ($slotsOccupes >= 2) {
             return response()->json(['message' => 'Garage complet (2/2 slots occupés)'], 400);
        }

        // Trouver le slot libre (1 ou 2)
        $slot1 = ReparationEnCours::where('statut', 'en_cours')->where('slot_garage', 1)->exists();
        $slot = $slot1 ? 2 : 1;

        $reparation->update([
            'statut' => 'en_cours',
            'slot_garage' => $slot,
            'date_debut' => now(),
            'technicien_id' => $request->user()->user_id
        ]);
        
        $this->firebaseService->syncReparation($reparation);

        return response()->json(['message' => 'Réparation commencée', 'reparation' => $reparation]);
    }

    /**
     * Mettre à jour la progression
     */
    public function updateProgression(Request $request, $id) {
        $reparation = ReparationEnCours::find($id);
        if (!$reparation) return response()->json(['message' => 'Non trouvé'], 404);

        $request->validate(['progression' => 'required|integer|min:0|max:100']);

        $reparation->update([
            'progression' => $request->progression
        ]);

        // Si 100%, on pourrait auto-terminer, mais laissons l'action explicite "terminer"
        $this->firebaseService->syncReparation($reparation);

        return response()->json(['message' => 'Progression mise à jour', 'reparation' => $reparation]);
    }

    /**
     * Terminer une réparation
     */
    public function terminer(Request $request, $id) {
        $reparation = ReparationEnCours::find($id);
        if (!$reparation) return response()->json(['message' => 'Non trouvé'], 404);

        $reparation->update([
            'statut' => 'terminee',
            'progression' => 100,
            'date_fin_estimee' => now(), // On utilise ça comme date de fin réelle ici
            'slot_garage' => null // Libère le slot
        ]);

        $this->firebaseService->syncReparation($reparation);
        // Optionnel : $this->firebaseService->removeReparation($id); si on veut l'enlever de la liste "en cours" du dashboard

        return response()->json(['message' => 'Réparation terminée', 'reparation' => $reparation]);
    }

    /**
     * Vérifier les slots disponibles
     */
    public function slotsDisponibles() {
        $slotsOccupes = ReparationEnCours::where('statut', 'en_cours')->count();
        return response()->json(['disponibles' => 2 - $slotsOccupes]);
    }
}
