<?php

// app/Http/Controllers/CategorieController.php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('produits')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_stockage' => 'required|in:Frais,Congelé,AmbiantSec,AmbiantHumide'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $categorie = Categorie::create($request->all());

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'categorie' => $categorie
        ], 201);
    }

    public function show($id)
    {
        $categorie = Categorie::with('produits')->findOrFail($id);
        return response()->json($categorie);
    }

    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'string|max:255',
            'type_stockage' => 'in:Frais,Congelé,AmbiantSec,AmbiantHumide'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $categorie->update($request->all());

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'categorie' => $categorie
        ]);
    }

    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);

        if ($categorie->produits()->count() > 0) {
            return response()->json([
                'error' => 'Impossible de supprimer une catégorie contenant des produits'
            ], 400);
        }

        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
