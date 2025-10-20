<?php

// app/Http/Controllers/PaiementController.php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with(['facture', 'client', 'fournisseur']);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->paginate(20);

        return response()->json($paiements);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facture_id' => 'nullable|exists:factures,id',
            'client_id' => 'nullable|exists:clients,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:Espèces,Chèque,Virement,MobileMoney,Carte',
            'reference' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $paiement = Paiement::create($request->all());

        return response()->json([
            'message' => 'Paiement enregistré avec succès',
            'paiement' => $paiement->load(['facture', 'client', 'fournisseur'])
        ], 201);
    }

    public function show($id)
    {
        $paiement = Paiement::with(['facture', 'client', 'fournisseur'])->findOrFail($id);
        return response()->json($paiement);
    }
}
