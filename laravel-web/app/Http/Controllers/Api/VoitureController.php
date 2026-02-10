<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voiture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoitureController extends Controller
{
    /**
     * Afficher la liste des voitures
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Si pas d'utilisateur authentifié (backoffice web) ou admin → toutes les voitures
        if (!$user || $user->role === 'admin') {
            $voitures = Voiture::with('user')->get();
        } else {
            // Sinon, seulement les voitures de l'utilisateur
            $voitures = Voiture::where('user_id', $user->user_id)->get();
        }

        
        return response()->json([
            'voitures' => $voitures
        ]);
    }

    /**
     * Ajouter une nouvelle voiture
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'immatriculation' => 'required|string|max:20|unique:voitures',
            'marque' => 'required|string|max:100',
            'modele' => 'required|string|max:100',
            'annee' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'couleur' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $voiture = Voiture::create([
            'user_id' => $request->user()->user_id,
            'immatriculation' => $request->immatriculation,
            'marque' => $request->marque,
            'modele' => $request->modele,
            'annee' => $request->annee,
            'couleur' => $request->couleur,
            'statut' => 'disponible'
        ]);

        return response()->json([
            'message' => 'Voiture ajoutée avec succès',
            'voiture' => $voiture
        ], 201);
    }

    /**
     * Afficher une voiture spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $voiture = Voiture::with(['user', 'interventions', 'reparationsEnCours'])->find($id);
        } else {
            $voiture = Voiture::with(['interventions', 'reparationsEnCours'])
                ->where('user_id', $user->user_id)
                ->find($id);
        }

        if (!$voiture) {
            return response()->json([
                'message' => 'Voiture non trouvée'
            ], 404);
        }

        return response()->json([
            'voiture' => $voiture
        ]);
    }

    /**
     * Mettre à jour une voiture
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $voiture = Voiture::find($id);
        } else {
            $voiture = Voiture::where('user_id', $user->user_id)->find($id);
        }

        if (!$voiture) {
            return response()->json([
                'message' => 'Voiture non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'immatriculation' => 'string|max:20|unique:voitures,immatriculation,' . $id . ',voiture_id',
            'marque' => 'string|max:100',
            'modele' => 'string|max:100',
            'annee' => 'integer|min:1900|max:' . (date('Y') + 1),
            'couleur' => 'string|max:50',
            'statut' => 'string|in:disponible,en_reparation,en_attente,hors_service'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $voiture->update($validator->validated());

        return response()->json([
            'message' => 'Voiture mise à jour avec succès',
            'voiture' => $voiture
        ]);
    }

    /**
     * Supprimer une voiture
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $voiture = Voiture::find($id);
        } else {
            $voiture = Voiture::where('user_id', $user->user_id)->find($id);
        }

        if (!$voiture) {
            return response()->json([
                'message' => 'Voiture non trouvée'
            ], 404);
        }

        // Vérifier si la voiture a des interventions en cours
        if ($voiture->interventions()->whereNull('date_fin')->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer une voiture avec des interventions en cours'
            ], 400);
        }

        $voiture->delete();

        return response()->json([
            'message' => 'Voiture supprimée avec succès'
        ]);
    }

    /**
     * Afficher les réparations d'une voiture
     */
    public function reparations(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $voiture = Voiture::find($id);
        } else {
            $voiture = Voiture::where('user_id', $user->user_id)->find($id);
        }

        if (!$voiture) {
            return response()->json([
                'message' => 'Voiture non trouvée'
            ], 404);
        }

        $reparations = $voiture->reparationsEnCours()->with('technicien')->get();

        return response()->json([
            'reparations' => $reparations
        ]);
    }
}