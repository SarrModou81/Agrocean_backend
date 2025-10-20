<?php

// app/Http/Controllers/VenteController.php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::with(['client', 'user', 'detailVentes.produit']);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_vente', [$request->date_debut, $request->date_fin]);
        }

        $ventes = $query->orderBy('date_vente', 'desc')->paginate(20);

        return response()->json($ventes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'date_vente' => 'required|date',
            'remise' => 'nullable|numeric|min:0',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier la disponibilité des stocks
        foreach ($request->produits as $item) {
            $produit = \App\Models\Produit::findOrFail($item['produit_id']);

            if (!$produit->verifierDisponibilite($item['quantite'])) {
                return response()->json([
                    'error' => "Stock insuffisant pour le produit: {$produit->nom}"
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            // Créer la vente
            $vente = Vente::create([
                'numero' => 'V' . date('Y') . str_pad(Vente::count() + 1, 6, '0', STR_PAD_LEFT),
                'client_id' => $request->client_id,
                'user_id' => auth()->id(),
                'date_vente' => $request->date_vente,
                'remise' => $request->remise ?? 0,
                'statut' => 'Brouillon'
            ]);

            // Créer les détails de vente
            foreach ($request->produits as $item) {
                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $item['produit_id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire']
                ]);

                // Réserver le stock
                $stock = Stock::where('produit_id', $item['produit_id'])
                    ->where('statut', 'Disponible')
                    ->orderBy('date_entree', 'asc')
                    ->first();

                if ($stock) {
                    $stock->ajusterQuantite(-$item['quantite']);
                }
            }

            // Calculer les totaux
            $vente->calculerTotal();

            DB::commit();

            return response()->json([
                'message' => 'Vente créée avec succès',
                'vente' => $vente->load(['client', 'detailVentes.produit'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de la création de la vente',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $vente = Vente::with([
            'client',
            'user',
            'detailVentes.produit',
            'facture',
            'livraison'
        ])->findOrFail($id);

        return response()->json($vente);
    }

    public function update(Request $request, $id)
    {
        $vente = Vente::findOrFail($id);

        if ($vente->statut == 'Livrée' || $vente->statut == 'Annulée') {
            return response()->json([
                'error' => 'Impossible de modifier une vente livrée ou annulée'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'remise' => 'nullable|numeric|min:0',
            'statut' => 'in:Brouillon,Validée,Livrée,Annulée'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $vente->update($request->all());

        if ($request->has('statut') && $request->statut == 'Validée') {
            $vente->genererFacture();
        }

        return response()->json([
            'message' => 'Vente mise à jour avec succès',
            'vente' => $vente->load(['client', 'detailVentes.produit'])
        ]);
    }

    public function valider($id)
    {
        $vente = Vente::findOrFail($id);

        if ($vente->statut != 'Brouillon') {
            return response()->json([
                'error' => 'Seules les ventes en brouillon peuvent être validées'
            ], 400);
        }

        $vente->statut = 'Validée';
        $vente->save();

        $facture = $vente->genererFacture();

        return response()->json([
            'message' => 'Vente validée avec succès',
            'vente' => $vente,
            'facture' => $facture
        ]);
    }

    public function annuler($id)
    {
        $vente = Vente::findOrFail($id);
        $vente->annuler();

        return response()->json([
            'message' => 'Vente annulée avec succès',
            'vente' => $vente
        ]);
    }

    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth());
        $dateFin = $request->input('date_fin', now()->endOfMonth());

        $ventes = Vente::whereBetween('date_vente', [$dateDebut, $dateFin])
            ->where('statut', '!=', 'Annulée')
            ->get();

        $stats = [
            'total_ventes' => $ventes->count(),
            'chiffre_affaires' => $ventes->sum('montant_ttc'),
            'ventes_par_jour' => $ventes->groupBy(function($vente) {
                return $vente->date_vente->format('Y-m-d');
            })->map(function($jour) {
                return [
                    'nombre' => $jour->count(),
                    'montant' => $jour->sum('montant_ttc')
                ];
            }),
            'top_clients' => $ventes->groupBy('client_id')
                ->map(function($ventesClient) {
                    return [
                        'client' => $ventesClient->first()->client->nom,
                        'nombre_ventes' => $ventesClient->count(),
                        'montant_total' => $ventesClient->sum('montant_ttc')
                    ];
                })
                ->sortByDesc('montant_total')
                ->take(10)
                ->values(),
            'produits_vendus' => DetailVente::whereIn('vente_id', $ventes->pluck('id'))
                ->with('produit')
                ->get()
                ->groupBy('produit_id')
                ->map(function($details) {
                    return [
                        'produit' => $details->first()->produit->nom,
                        'quantite' => $details->sum('quantite'),
                        'montant' => $details->sum('sous_total')
                    ];
                })
                ->sortByDesc('quantite')
                ->take(10)
                ->values()
        ];

        return response()->json($stats);
    }
}
