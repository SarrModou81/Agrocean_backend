<?php
// app/Http/Controllers/FournisseurController.php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FournisseurController extends Controller
{
    /**
     * Afficher la liste des fournisseurs
     */
    public function index()
    {
        $fournisseurs = Fournisseur::withCount('commandeAchats')
            ->with(['commandeAchats' => function($query) {
                $query->latest()->limit(5);
            }])
            ->paginate(20);

        return response()->json($fournisseurs);
    }

    /**
     * Créer un nouveau fournisseur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
            'conditions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $fournisseur = Fournisseur::create([
            'nom' => $request->nom,
            'contact' => $request->contact,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'evaluation' => 0,
            'conditions' => $request->conditions
        ]);

        return response()->json([
            'message' => 'Fournisseur créé avec succès',
            'fournisseur' => $fournisseur
        ], 201);
    }

    /**
     * Afficher un fournisseur spécifique
     */
    public function show($id)
    {
        $fournisseur = Fournisseur::with([
            'commandeAchats' => function($query) {
                $query->orderBy('date_commande', 'desc')->limit(10);
            },
            'paiements'
        ])->findOrFail($id);

        // Calculer l'évaluation en temps réel
        $fournisseur->evaluation_calculee = $fournisseur->evaluer();

        // Statistiques
        $totalCommandes = $fournisseur->commandeAchats->count();
        $commandesRecues = $fournisseur->commandeAchats()
            ->where('statut', 'Reçue')
            ->count();
        $montantTotal = $fournisseur->commandeAchats()
            ->where('statut', 'Reçue')
            ->sum('montant_total');

        $fournisseur->statistiques = [
            'total_commandes' => $totalCommandes,
            'commandes_recues' => $commandesRecues,
            'montant_total' => $montantTotal,
            'taux_livraison' => $totalCommandes > 0
                ? round(($commandesRecues / $totalCommandes) * 100, 2)
                : 0
        ];

        return response()->json($fournisseur);
    }

    /**
     * Mettre à jour un fournisseur
     */
    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
            'adresse' => 'sometimes|string',
            'evaluation' => 'sometimes|numeric|min:0|max:5',
            'conditions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $fournisseur->update($request->only([
            'nom',
            'contact',
            'telephone',
            'adresse',
            'evaluation',
            'conditions'
        ]));

        return response()->json([
            'message' => 'Fournisseur mis à jour avec succès',
            'fournisseur' => $fournisseur
        ]);
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        // Vérifier s'il y a des commandes associées
        if ($fournisseur->commandeAchats()->count() > 0) {
            return response()->json([
                'error' => 'Impossible de supprimer un fournisseur ayant des commandes'
            ], 400);
        }

        $fournisseur->delete();

        return response()->json([
            'message' => 'Fournisseur supprimé avec succès'
        ]);
    }

    /**
     * Obtenir l'historique des commandes d'un fournisseur
     */
    public function historique($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $commandes = $fournisseur->commandeAchats()
            ->with([
                'detailCommandeAchats.produit',
                'user'
            ])
            ->orderBy('date_commande', 'desc')
            ->paginate(20);

        return response()->json([
            'fournisseur' => [
                'id' => $fournisseur->id,
                'nom' => $fournisseur->nom
            ],
            'commandes' => $commandes
        ]);
    }

    /**
     * Évaluer un fournisseur manuellement
     */
    public function evaluer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'evaluation' => 'required|numeric|min:0|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->evaluation = $request->evaluation;
        $fournisseur->save();

        return response()->json([
            'message' => 'Évaluation mise à jour avec succès',
            'fournisseur' => $fournisseur
        ]);
    }

    /**
     * Obtenir les fournisseurs les mieux notés
     */
    public function topFournisseurs()
    {
        $fournisseurs = Fournisseur::orderBy('evaluation', 'desc')
            ->limit(10)
            ->get();

        return response()->json($fournisseurs);
    }

    /**
     * Rechercher des fournisseurs
     */
    public function rechercher(Request $request)
    {
        $query = Fournisseur::query();

        if ($request->has('nom')) {
            $query->where('nom', 'ILIKE', '%' . $request->nom . '%');
        }

        if ($request->has('telephone')) {
            $query->where('telephone', 'ILIKE', '%' . $request->telephone . '%');
        }

        if ($request->has('evaluation_min')) {
            $query->where('evaluation', '>=', $request->evaluation_min);
        }

        $fournisseurs = $query->paginate(20);

        return response()->json($fournisseurs);
    }
}
