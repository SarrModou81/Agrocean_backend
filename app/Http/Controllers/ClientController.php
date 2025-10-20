<?php

// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('ventes')->paginate(20);
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'required|string',
            'adresse' => 'required|string',
            'type' => 'required|in:Menage,Boutique,GrandeSurface,Restaurant,Institution',
            'credit_max' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $client = Client::create($request->all());

        return response()->json([
            'message' => 'Client créé avec succès',
            'client' => $client
        ], 201);
    }

    public function show($id)
    {
        $client = Client::with(['ventes', 'paiements'])->findOrFail($id);
        $client->solde_reel = $client->calculerSolde();

        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'string',
            'adresse' => 'string',
            'type' => 'in:Menage,Boutique,GrandeSurface,Restaurant,Institution',
            'credit_max' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $client->update($request->all());

        return response()->json([
            'message' => 'Client mis à jour avec succès',
            'client' => $client
        ]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'message' => 'Client supprimé avec succès'
        ]);
    }

    public function historique($id)
    {
        $client = Client::findOrFail($id);
        $ventes = $client->ventes()
            ->with(['detailVentes.produit', 'facture'])
            ->orderBy('date_vente', 'desc')
            ->get();

        return response()->json($ventes);
    }
}
