<?php

// app/Http/Controllers/StockController.php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Produit;
use App\Models\Entrepot;
use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Afficher la liste des stocks
     */
    public function index(Request $request)
    {
        $query = Stock::with(['produit.categorie', 'entrepot']);

        // Filtres
        if ($request->has('entrepot_id')) {
            $query->where('entrepot_id', $request->entrepot_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        }

        // Recherche par nom de produit
        if ($request->has('search')) {
            $query->whereHas('produit', function($q) use ($request) {
                $q->where('nom', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('code', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $stocks = $query->orderBy('date_entree', 'desc')->paginate(20);

        // Ajouter des informations calculées
        $stocks->getCollection()->transform(function($stock) {
            $stock->valeur = $stock->calculerValeur();
            $stock->etat_peremption = $stock->verifierPeremption();
            return $stock;
        });

        return response()->json($stocks);
    }

    /**
     * Créer une nouvelle entrée de stock
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produit_id' => 'required|exists:produits,id',
            'entrepot_id' => 'required|exists:entrepots,id',
            'quantite' => 'required|integer|min:1',
            'emplacement' => 'required|string|max:255',
            'numero_lot' => 'nullable|string|max:255',
            'date_peremption' => 'nullable|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        // Vérifier la capacité de l'entrepôt
        $entrepot = Entrepot::findOrFail($request->entrepot_id);
        $capaciteDisponible = $entrepot->verifierCapacite();

        if ($capaciteDisponible < $request->quantite) {
            return response()->json([
                'error' => 'Capacité de l\'entrepôt insuffisante',
                'capacite_disponible' => $capaciteDisponible,
                'quantite_demandee' => $request->quantite
            ], 400);
        }

        // Créer l'entrée de stock
        $stock = Stock::create([
            'produit_id' => $request->produit_id,
            'entrepot_id' => $request->entrepot_id,
            'quantite' => $request->quantite,
            'emplacement' => $request->emplacement,
            'date_entree' => now(),
            'numero_lot' => $request->numero_lot ?? 'LOT' . date('YmdHis') . rand(1000, 9999),
            'date_peremption' => $request->date_peremption,
            'statut' => 'Disponible'
        ]);

        return response()->json([
            'message' => 'Entrée de stock enregistrée avec succès',
            'stock' => $stock->load(['produit', 'entrepot'])
        ], 201);
    }

    /**
     * Afficher un stock spécifique
     */
    public function show($id)
    {
        $stock = Stock::with(['produit.categorie', 'entrepot'])->findOrFail($id);

        $stock->valeur = $stock->calculerValeur();
        $stock->etat_peremption = $stock->verifierPeremption();

        // Historique des mouvements pour ce lot
        $stock->historique = Stock::where('numero_lot', $stock->numero_lot)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($stock);
    }

    /**
     * Mettre à jour un stock
     */
    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quantite' => 'sometimes|integer|min:0',
            'emplacement' => 'sometimes|string|max:255',
            'statut' => 'sometimes|in:Disponible,Réservé,Périmé,Endommagé',
            'date_peremption' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $stock->update($request->only([
            'quantite',
            'emplacement',
            'statut',
            'date_peremption'
        ]));

        return response()->json([
            'message' => 'Stock mis à jour avec succès',
            'stock' => $stock->load(['produit', 'entrepot'])
        ]);
    }

    /**
     * Ajuster la quantité d'un stock
     */
    public function ajusterStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ajustement' => 'required|integer',
            'motif' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $stock = Stock::findOrFail($id);
        $ancienneQuantite = $stock->quantite;
        $nouvelleQuantite = $ancienneQuantite + $request->ajustement;

        if ($nouvelleQuantite < 0) {
            return response()->json([
                'error' => 'La quantité ne peut pas être négative',
                'quantite_actuelle' => $ancienneQuantite,
                'ajustement_demande' => $request->ajustement
            ], 400);
        }

        $stock->ajusterQuantite($request->ajustement);

        // Log de l'ajustement
        \Log::info('Ajustement de stock', [
            'stock_id' => $stock->id,
            'produit' => $stock->produit->nom,
            'ancienne_quantite' => $ancienneQuantite,
            'nouvelle_quantite' => $nouvelleQuantite,
            'ajustement' => $request->ajustement,
            'motif' => $request->motif,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Ajustement de stock effectué avec succès',
            'stock' => $stock,
            'ancienne_quantite' => $ancienneQuantite,
            'nouvelle_quantite' => $nouvelleQuantite
        ]);
    }

    /**
     * Vérifier les péremptions
     */
    public function verifierPeremptions()
    {
        $stocks = Stock::where('statut', 'Disponible')
            ->whereNotNull('date_peremption')
            ->get();

        $produitsExpires = [];
        $produitsAlerte = [];

        foreach ($stocks as $stock) {
            $etat = $stock->verifierPeremption();

            if ($etat == 'expired') {
                $produitsExpires[] = [
                    'stock' => $stock,
                    'produit' => $stock->produit->nom,
                    'lot' => $stock->numero_lot,
                    'date_peremption' => $stock->date_peremption,
                    'quantite' => $stock->quantite
                ];

                // Créer une alerte
                Alerte::firstOrCreate([
                    'type' => 'Péremption',
                    'produit_id' => $stock->produit_id,
                    'lue' => false
                ], [
                    'message' => "Le produit {$stock->produit->nom} (Lot: {$stock->numero_lot}) est périmé depuis le " . $stock->date_peremption->format('d/m/Y')
                ]);
            }
            elseif ($etat == 'warning') {
                $joursRestants = Carbon::now()->diffInDays($stock->date_peremption);

                $produitsAlerte[] = [
                    'stock' => $stock,
                    'produit' => $stock->produit->nom,
                    'lot' => $stock->numero_lot,
                    'date_peremption' => $stock->date_peremption,
                    'jours_restants' => $joursRestants,
                    'quantite' => $stock->quantite
                ];

                // Créer une alerte
                Alerte::firstOrCreate([
                    'type' => 'Péremption',
                    'produit_id' => $stock->produit_id,
                    'lue' => false
                ], [
                    'message' => "Le produit {$stock->produit->nom} (Lot: {$stock->numero_lot}) expire dans {$joursRestants} jours"
                ]);
            }
        }

        return response()->json([
            'message' => 'Vérification des péremptions effectuée',
            'produits_expires' => $produitsExpires,
            'produits_alerte' => $produitsAlerte,
            'total_expires' => count($produitsExpires),
            'total_alerte' => count($produitsAlerte)
        ]);
    }

    /**
     * Générer un inventaire complet
     */
    public function inventaire(Request $request)
    {
        $query = Stock::with(['produit.categorie', 'entrepot'])
            ->where('statut', 'Disponible');

        if ($request->has('entrepot_id')) {
            $query->where('entrepot_id', $request->entrepot_id);
        }

        $stocks = $query->get();

        $valeurTotale = 0;
        $parEntrepot = [];
        $parCategorie = [];

        foreach ($stocks as $stock) {
            $valeur = $stock->calculerValeur();
            $valeurTotale += $valeur;

            // Par entrepôt
            $entrepotId = $stock->entrepot_id;
            if (!isset($parEntrepot[$entrepotId])) {
                $parEntrepot[$entrepotId] = [
                    'entrepot' => $stock->entrepot->nom,
                    'quantite_totale' => 0,
                    'valeur_totale' => 0,
                    'nombre_produits' => 0
                ];
            }
            $parEntrepot[$entrepotId]['quantite_totale'] += $stock->quantite;
            $parEntrepot[$entrepotId]['valeur_totale'] += $valeur;
            $parEntrepot[$entrepotId]['nombre_produits']++;

            // Par catégorie
            $categorieId = $stock->produit->categorie_id;
            if (!isset($parCategorie[$categorieId])) {
                $parCategorie[$categorieId] = [
                    'categorie' => $stock->produit->categorie->nom,
                    'quantite_totale' => 0,
                    'valeur_totale' => 0,
                    'nombre_produits' => 0
                ];
            }
            $parCategorie[$categorieId]['quantite_totale'] += $stock->quantite;
            $parCategorie[$categorieId]['valeur_totale'] += $valeur;
            $parCategorie[$categorieId]['nombre_produits']++;
        }

        return response()->json([
            'date_inventaire' => now()->format('Y-m-d H:i:s'),
            'total_produits' => $stocks->count(),
            'quantite_totale' => $stocks->sum('quantite'),
            'valeur_totale' => $valeurTotale,
            'par_entrepot' => array_values($parEntrepot),
            'par_categorie' => array_values($parCategorie),
            'details' => $stocks
        ]);
    }

    /**
     * Tracer un produit
     */
    public function tracerProduit($produitId)
    {
        $produit = Produit::findOrFail($produitId);

        $tracabilite = [
            'produit' => [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'code' => $produit->code
            ],
            'stocks_actuels' => Stock::where('produit_id', $produitId)
                ->where('statut', 'Disponible')
                ->with('entrepot')
                ->get()
                ->map(function($stock) {
                    return [
                        'lot' => $stock->numero_lot,
                        'entrepot' => $stock->entrepot->nom,
                        'quantite' => $stock->quantite,
                        'emplacement' => $stock->emplacement,
                        'date_entree' => $stock->date_entree,
                        'date_peremption' => $stock->date_peremption,
                        'valeur' => $stock->calculerValeur()
                    ];
                }),
            'historique_mouvements' => Stock::where('produit_id', $produitId)
                ->with('entrepot')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function($stock) {
                    return [
                        'date' => $stock->created_at,
                        'type' => $stock->statut,
                        'lot' => $stock->numero_lot,
                        'entrepot' => $stock->entrepot->nom,
                        'quantite' => $stock->quantite
                    ];
                }),
            'stock_total_disponible' => $produit->stockTotal()
        ];

        return response()->json($tracabilite);
    }

    /**
     * Mouvements de stock par période
     */
    public function mouvementsPeriode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $mouvements = Stock::whereBetween('created_at', [
            $request->date_debut,
            $request->date_fin
        ])
            ->with(['produit', 'entrepot'])
            ->orderBy('created_at', 'desc')
            ->get();

        $entrees = $mouvements->sum('quantite');
        $valeurEntrees = $mouvements->sum(function($stock) {
            return $stock->calculerValeur();
        });

        return response()->json([
            'periode' => [
                'debut' => $request->date_debut,
                'fin' => $request->date_fin
            ],
            'total_mouvements' => $mouvements->count(),
            'total_entrees_quantite' => $entrees,
            'valeur_totale_entrees' => $valeurEntrees,
            'mouvements' => $mouvements
        ]);
    }

    /**
     * Supprimer un stock (soft delete ou réel selon besoin)
     */
    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);

        if ($stock->quantite > 0 && $stock->statut == 'Disponible') {
            return response()->json([
                'error' => 'Impossible de supprimer un stock disponible avec des quantités',
                'suggestion' => 'Ajustez d\'abord la quantité à 0 ou changez le statut'
            ], 400);
        }

        $stock->delete();

        return response()->json([
            'message' => 'Stock supprimé avec succès'
        ]);
    }
}
