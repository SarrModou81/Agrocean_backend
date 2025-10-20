<?php

// app/Http/Controllers/LivraisonController.php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LivraisonController extends Controller
{
    /**
     * Liste des livraisons
     */
    public function index(Request $request)
    {
        $query = Livraison::with(['vente.client']);

        // Filtrer par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par date
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_prevue', [$request->date_debut, $request->date_fin]);
        }

        // Livraisons du jour
        if ($request->has('aujourdhui')) {
            $query->whereDate('date_prevue', today());
        }

        $livraisons = $query->orderBy('date_prevue', 'asc')->paginate(20);

        return response()->json($livraisons);
    }

    /**
     * Créer une livraison
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vente_id' => 'required|exists:ventes,id',
            'date_prevue' => 'required|date',
            'adresse' => 'required|string',
            'livreur' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        // Vérifier que la vente n'a pas déjà une livraison
        $vente = Vente::findOrFail($request->vente_id);
        if ($vente->livraison) {
            return response()->json([
                'error' => 'Cette vente a déjà une livraison planifiée'
            ], 400);
        }

        $livraison = Livraison::create([
            'vente_id' => $request->vente_id,
            'date_prevue' => $request->date_prevue,
            'adresse' => $request->adresse,
            'livreur' => $request->livreur,
            'statut' => 'Planifiée'
        ]);

        return response()->json([
            'message' => 'Livraison planifiée avec succès',
            'livraison' => $livraison->load(['vente.client'])
        ], 201);
    }

    /**
     * Détails d'une livraison
     */
    public function show($id)
    {
        $livraison = Livraison::with(['vente.client', 'vente.detailVentes.produit'])
            ->findOrFail($id);

        return response()->json($livraison);
    }

    /**
     * Mettre à jour une livraison
     */
    public function update(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date_prevue' => 'sometimes|date',
            'adresse' => 'sometimes|string',
            'livreur' => 'nullable|string',
            'statut' => 'sometimes|in:Planifiée,EnCours,Livrée,Annulée'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $livraison->update($request->only([
            'date_prevue',
            'adresse',
            'livreur',
            'statut'
        ]));

        return response()->json([
            'message' => 'Livraison mise à jour avec succès',
            'livraison' => $livraison->load(['vente.client'])
        ]);
    }

    /**
     * Démarrer une livraison
     */
    public function demarrer($id)
    {
        $livraison = Livraison::findOrFail($id);

        if ($livraison->statut !== 'Planifiée') {
            return response()->json([
                'error' => 'Seules les livraisons planifiées peuvent être démarrées'
            ], 400);
        }

        $livraison->statut = 'EnCours';
        $livraison->save();

        return response()->json([
            'message' => 'Livraison démarrée',
            'livraison' => $livraison
        ]);
    }

    /**
     * Confirmer une livraison
     */
    public function confirmer($id)
    {
        $livraison = Livraison::findOrFail($id);

        if ($livraison->statut === 'Livrée') {
            return response()->json([
                'error' => 'Cette livraison a déjà été confirmée'
            ], 400);
        }

        $livraison->confirmer();

        return response()->json([
            'message' => 'Livraison confirmée avec succès',
            'livraison' => $livraison->load(['vente'])
        ]);
    }

    /**
     * Annuler une livraison
     */
    public function annuler($id)
    {
        $livraison = Livraison::findOrFail($id);

        if ($livraison->statut === 'Livrée') {
            return response()->json([
                'error' => 'Impossible d\'annuler une livraison déjà effectuée'
            ], 400);
        }

        $livraison->statut = 'Annulée';
        $livraison->save();

        return response()->json([
            'message' => 'Livraison annulée',
            'livraison' => $livraison
        ]);
    }

    /**
     * Livraisons du jour
     */
    public function aujourdhui()
    {
        $livraisons = Livraison::with(['vente.client'])
            ->whereDate('date_prevue', today())
            ->whereIn('statut', ['Planifiée', 'EnCours'])
            ->orderBy('date_prevue', 'asc')
            ->get();

        return response()->json([
            'date' => today()->format('Y-m-d'),
            'total' => $livraisons->count(),
            'livraisons' => $livraisons
        ]);
    }

    /**
     * Statistiques des livraisons
     */
    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth());
        $dateFin = $request->input('date_fin', now()->endOfMonth());

        $stats = [
            'periode' => [
                'debut' => $dateDebut,
                'fin' => $dateFin
            ],
            'total' => Livraison::whereBetween('date_prevue', [$dateDebut, $dateFin])->count(),
            'livrees' => Livraison::whereBetween('date_prevue', [$dateDebut, $dateFin])
                ->where('statut', 'Livrée')->count(),
            'en_cours' => Livraison::whereBetween('date_prevue', [$dateDebut, $dateFin])
                ->where('statut', 'EnCours')->count(),
            'planifiees' => Livraison::whereBetween('date_prevue', [$dateDebut, $dateFin])
                ->where('statut', 'Planifiée')->count(),
            'annulees' => Livraison::whereBetween('date_prevue', [$dateDebut, $dateFin])
                ->where('statut', 'Annulée')->count()
        ];

        $stats['taux_reussite'] = $stats['total'] > 0
            ? round(($stats['livrees'] / $stats['total']) * 100, 2)
            : 0;

        return response()->json($stats);
    }
}
