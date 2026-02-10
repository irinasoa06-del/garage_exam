<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Voiture;
use App\Models\TypeIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterventionController extends Controller
{
    /**
     * Afficher la liste des interventions
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'admin' || $user->role === 'mecanicien') {
            // Admin et mécaniciens voient toutes les interventions
            $interventions = Intervention::with(['voiture.user', 'type'])
                ->orderBy('priorite', 'desc')
                ->orderBy('date_signalement', 'desc')
                ->get();
        } else {
            // Clients voient seulement leurs interventions
            $interventions = Intervention::with(['voiture', 'type'])
                ->whereHas('voiture', function ($query) use ($user) {
                    $query->where('user_id', $user->user_id);
                })
                ->orderBy('date_signalement', 'desc')
                ->get();
        }

        return response()->json([
            'interventions' => $interventions
        ]);
    }

    /**
     * Créer une nouvelle intervention
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voiture_id' => 'required|exists:voitures,voiture_id',
            'type_id' => 'required|exists:type_interventions,type_id',
            'description_panne' => 'required|string|max:1000',
            'priorite' => 'required|string|in:haute,moyenne,basse',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que la voiture appartient à l'utilisateur (sauf admin)
        $user = $request->user();
        if ($user->role !== 'admin') {
            $voiture = Voiture::where('voiture_id', $request->voiture_id)
                ->where('user_id', $user->user_id)
                ->first();
            
            if (!$voiture) {
                return response()->json([
                    'message' => 'Voiture non trouvée ou non autorisée'
                ], 403);
            }
        }

        $intervention = Intervention::create([
            'voiture_id' => $request->voiture_id,
            'type_id' => $request->type_id,
            'description_panne' => $request->description_panne,
            'priorite' => $request->priorite,
            'date_signalement' => now(),
        ]);

        $intervention->load(['voiture', 'type']);

        return response()->json([
            'message' => 'Intervention créée avec succès',
            'intervention' => $intervention
        ], 201);
    }

    /**
     * Créer une intervention pour une voiture spécifique
     */
    public function storeForVoiture(Request $request, $voitureId)
    {
        $user = $request->user();
        
        // Vérifier que la voiture appartient à l'utilisateur (sauf admin)
        if ($user->role !== 'admin') {
            $voiture = Voiture::where('voiture_id', $voitureId)
                ->where('user_id', $user->user_id)
                ->first();
            
            if (!$voiture) {
                return response()->json([
                    'message' => 'Voiture non trouvée ou non autorisée'
                ], 403);
            }
        } else {
            $voiture = Voiture::find($voitureId);
            if (!$voiture) {
                return response()->json([
                    'message' => 'Voiture non trouvée'
                ], 404);
            }
        }

        $validator = Validator::make($request->all(), [
            'type_id' => 'required|exists:type_interventions,type_id',
            'description_panne' => 'required|string|max:1000',
            'priorite' => 'required|string|in:haute,moyenne,basse',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $intervention = Intervention::create([
            'voiture_id' => $voitureId,
            'type_id' => $request->type_id,
            'description_panne' => $request->description_panne,
            'priorite' => $request->priorite,
            'date_signalement' => now(),
        ]);

        $intervention->load(['voiture', 'type']);

        return response()->json([
            'message' => 'Intervention créée avec succès',
            'intervention' => $intervention
        ], 201);
    }

    /**
     * Afficher une intervention spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin' || $user->role === 'mecanicien') {
            $intervention = Intervention::with(['voiture.user', 'type'])->find($id);
        } else {
            $intervention = Intervention::with(['voiture', 'type'])
                ->where('intervention_id', $id)
                ->whereHas('voiture', function ($query) use ($user) {
                    $query->where('user_id', $user->user_id);
                })
                ->first();
        }

        if (!$intervention) {
            return response()->json([
                'message' => 'Intervention non trouvée'
            ], 404);
        }

        return response()->json([
            'intervention' => $intervention
        ]);
    }

    /**
     * Mettre à jour une intervention
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin' || $user->role === 'mecanicien') {
            $intervention = Intervention::find($id);
        } else {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        if (!$intervention) {
            return response()->json([
                'message' => 'Intervention non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'description_panne' => 'string|max:1000',
            'priorite' => 'string|in:haute,moyenne,basse',
            'date_fin' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $intervention->update($validator->validated());

        return response()->json([
            'message' => 'Intervention mise à jour avec succès',
            'intervention' => $intervention
        ]);
    }

    /**
     * Supprimer une intervention
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        $intervention = Intervention::find($id);

        if (!$intervention) {
            return response()->json([
                'message' => 'Intervention non trouvée'
            ], 404);
        }

        $intervention->delete();

        return response()->json([
            'message' => 'Intervention supprimée avec succès'
        ]);
    }

    /**
     * Récupérer les types d'intervention
     */
    public function types()
    {
        $types = TypeIntervention::all();
        
        return response()->json([
            'types' => $types
        ]);
    }
}