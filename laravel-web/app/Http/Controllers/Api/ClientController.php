<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReparationEnCours;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste tous les clients avec leurs voitures
     */
    public function index(Request $request)
    {
        // Récupérer tous les utilisateurs qui ont le rôle 'client'
        $clients = User::where('role', 'client')
            ->with(['voitures'])
            ->get();

        return response()->json([
            'clients' => $clients
        ]);
    }

    /**
     * Afficher les détails d'un client spécifique
     */
    public function show(Request $request, $id)
    {
        $client = User::where('role', 'client')
            ->where('user_id', $id)
            ->with(['voitures'])
            ->first();

        if (!$client) {
            return response()->json([
                'message' => 'Client non trouvé'
            ], 404);
        }

        return response()->json([
            'client' => $client
        ]);
    }

    /**
     * Récupérer l'historique des réparations d'un client
     * Affiche toutes les réparations (terminées et en cours) de toutes les voitures du client
     * avec le montant et la date
     */
    public function repairHistory(Request $request, $id)
    {
        $client = User::where('role', 'client')
            ->where('user_id', $id)
            ->first();

        if (!$client) {
            return response()->json([
                'message' => 'Client non trouvé'
            ], 404);
        }

        // Récupérer toutes les réparations des voitures du client
        $reparations = ReparationEnCours::whereHas('voiture', function($query) use ($id) {
                $query->where('user_id', $id);
            })
            ->with(['voiture', 'type', 'technicien'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les statistiques
        $totalDepense = $reparations->sum(function($rep) {
            return $rep->type->prix_unitaire ?? 0;
        });

        $nombreReparations = $reparations->count();
        $reparationsTerminees = $reparations->where('statut', 'terminee')->count();

        // Formater les données pour l'affichage
        $historique = $reparations->map(function($rep) {
            return [
                'reparation_id' => $rep->reparation_id,
                'date' => $rep->created_at,
                'date_debut' => $rep->date_debut,
                'date_fin' => $rep->date_fin_estimee,
                'voiture' => [
                    'marque' => $rep->voiture->marque,
                    'modele' => $rep->voiture->modele,
                    'immatriculation' => $rep->voiture->immatriculation,
                ],
                'type_intervention' => $rep->type->nom,
                'montant' => $rep->type->prix_unitaire,
                'statut' => $rep->statut,
                'progression' => $rep->progression,
                'technicien' => $rep->technicien ? $rep->technicien->nom . ' ' . $rep->technicien->prenom : null
            ];
        });

        return response()->json([
            'client' => [
                'user_id' => $client->user_id,
                'nom' => $client->nom,
                'prenom' => $client->prenom,
                'email' => $client->email,
                'telephone' => $client->telephone
            ],
            'statistiques' => [
                'total_depense' => $totalDepense,
                'nombre_reparations' => $nombreReparations,
                'reparations_terminees' => $reparationsTerminees
            ],
            'historique' => $historique
        ]);
    }
}
