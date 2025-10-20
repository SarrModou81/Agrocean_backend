<?php

// app/Http/Controllers/CommandeAchatController.php

namespace App\Http\Controllers;

use App\Models\CommandeAchat;
use App\Models\DetailCommandeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandeAchatController extends Controller
{
    public function index(Request $request)
    {
        $query = CommandeAchat::with(['fournisseur', 'user', 'detailCommandeAchats.produit']);

        if ($request->has('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $commandes = $query->orderBy('date_commande', 'desc')->paginate(20);

        return response()->json($commandes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $commande = CommandeAchat::create([
                'numero' => 'CA' . date('Y') . str_pad(CommandeAchat::count() + 1, 6, '0', STR_PAD_LEFT),
                'fournisseur_id' => $request->fournisseur_id,
                'user_id' => auth()->id(),
                'date_commande' => $request->date_commande,
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'statut' => 'Brouillon',
                'montant_total' => 0
            ]);

            foreach ($request->produits as $item) {
                DetailCommandeAchat::create([
                    'commande_achat_id' => $commande->id,
                    'produit_id' => $item['produit_id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire']
                ]);
            }

            $commande->calculerTotal();

            DB::commit();

            return response()->json([
                'message' => 'Commande d\'achat créée avec succès',
                'commande' => $commande->load(['fournisseur', 'detailCommandeAchats.produit'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de la création de la commande',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $commande = CommandeAchat::with([
            'fournisseur',
            'user',
            'detailCommandeAchats.produit'
        ])->findOrFail($id);

        return response()->json($commande);
    }

    public function valider($id)
    {
        $commande = CommandeAchat::findOrFail($id);

        if ($commande->statut != 'Brouillon') {
            return response()->json([
                'error' => 'Seules les commandes en brouillon peuvent être validées'
            ], 400);
        }

        $commande->valider();

        return response()->json([
            'message' => 'Commande validée avec succès',
            'commande' => $commande
        ]);
    }

    public function receptionner(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'entrepot_id' => 'required|exists:entrepots,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $commande = CommandeAchat::findOrFail($id);

        if ($commande->statut != 'Validée' && $commande->statut != 'EnCours') {
            return response()->json([
                'error' => 'Impossible de réceptionner cette commande'
            ], 400);
        }

        $commande->receptionner($request->entrepot_id);

        return response()->json([
            'message' => 'Commande réceptionnée avec succès',
            'commande' => $commande
        ]);
    }
}
