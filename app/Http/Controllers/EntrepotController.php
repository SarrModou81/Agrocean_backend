<?php

// app/Http/Controllers/EntrepotController.php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntrepotController extends Controller
{
    public function index()
    {
        $entrepots = Entrepot::withCount('stocks')->get();

        $entrepots->each(function($entrepot) {
            $entrepot->capacite_disponible = $entrepot->verifierCapacite();
        });

        return response()->json($entrepots);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'capacite' => 'required|integer|min:1',
            'type_froid' => 'required|in:Frais,Congelé,Ambiant,Mixte'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $entrepot = Entrepot::create($request->all());

        return response()->json([
            'message' => 'Entrepôt créé avec succès',
            'entrepot' => $entrepot
        ], 201);
    }

    public function show($id)
    {
        $entrepot = Entrepot::with('stocks.produit')->findOrFail($id);
        $entrepot->capacite_disponible = $entrepot->verifierCapacite();

        return response()->json($entrepot);
    }

    public function update(Request $request, $id)
    {
        $entrepot = Entrepot::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'string|max:255',
            'adresse' => 'string',
            'capacite' => 'integer|min:1',
            'type_froid' => 'in:Frais,Congelé,Ambiant,Mixte'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $entrepot->update($request->all());

        return response()->json([
            'message' => 'Entrepôt mis à jour avec succès',
            'entrepot' => $entrepot
        ]);
    }
}
