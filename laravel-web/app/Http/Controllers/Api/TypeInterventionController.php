<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeInterventionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = TypeIntervention::all();
        return response()->json([
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'duree_secondes' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = TypeIntervention::create($validator->validated());

        return response()->json([
            'message' => 'Type d\'intervention créé avec succès',
            'type' => $type
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $type = TypeIntervention::find($id);

        if (!$type) {
            return response()->json(['message' => 'Type d\'intervention non trouvé'], 404);
        }

        return response()->json(['type' => $type]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $type = TypeIntervention::find($id);

        if (!$type) {
            return response()->json(['message' => 'Type d\'intervention non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string|max:255',
            'prix_unitaire' => 'sometimes|required|numeric|min:0',
            'duree_secondes' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type->update($validator->validated());

        return response()->json([
            'message' => 'Type d\'intervention mis à jour avec succès',
            'type' => $type
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $type = TypeIntervention::find($id);

        if (!$type) {
            return response()->json(['message' => 'Type d\'intervention non trouvé'], 404);
        }

        // Check for dependencies? Maybe later. For now, basic delete.
        $type->delete();

        return response()->json(['message' => 'Type d\'intervention supprimé avec succès']);
    }
}
