<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\ReparationEnCours;
use App\Models\TypeIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandeController extends Controller
{
    /**
     * Liste des commandes
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $commandes = Commande::with(['user', 'voiture'])->orderBy('date_commande', 'desc')->get();
        } else {
            $commandes = Commande::with(['voiture'])->where('user_id', $user->user_id)->orderBy('date_commande', 'desc')->get();
        }

        return response()->json(['commandes' => $commandes]);
    }

    /**
     * Créer une commande (générer facture)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voiture_id' => 'required|exists:voitures,voiture_id',
            'reparations' => 'required|array', // IDs des réparations
            'reparations.*' => 'exists:reparations_en_cours,reparation_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $total = 0;
            $user = $request->user();

            // Créer commande
            $commande = Commande::create([
                'user_id' => $user->user_id,
                'voiture_id' => $request->voiture_id,
                'date_commande' => now(),
                'statut_paiement' => 'en_attente'
            ]);

            foreach ($request->reparations as $reparationId) {
                $reparation = ReparationEnCours::find($reparationId);
                $type = TypeIntervention::find($reparation->type_id);
                
                $prix = $type->prix_unitaire;
                $total += $prix;

                CommandeDetail::create([
                    'commande_id' => $commande->commande_id,
                    'reparation_id' => $reparationId,
                    'type_id' => $type->type_id,
                    'prix_unitaire' => $prix,
                    'sous_total' => $prix
                ]);
            }

            $commande->update(['montant_total' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'Commande créée',
                'commande' => $commande->load('details')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur création commande', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Voir la facture
     */
    public function facture(Request $request, $id)
    {
        $commande = Commande::with(['details.type', 'user', 'voiture'])->find($id);

        if (!$commande) {
            return response()->json(['message' => 'Non trouvé'], 404);
        }

        return response()->json(['facture' => $commande]);
    }

    /**
     * Simuler le paiement
     */
    public function processPaiement(Request $request, $id)
    {
        $commande = Commande::find($id);
        if (!$commande) return response()->json(['message' => 'Non trouvé'], 404);

        if ($commande->statut_paiement === 'paye') {
            return response()->json(['message' => 'Déjà payé']);
        }

        $commande->update([
            'statut_paiement' => 'paye',
            'date_paiement' => now(),
            'mode_paiement' => 'carte' // Mock
        ]);

        return response()->json(['message' => 'Paiement effectué', 'commande' => $commande]);
    }
}
