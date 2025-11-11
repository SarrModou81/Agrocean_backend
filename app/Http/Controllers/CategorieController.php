<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'code_prefix' => 'nullable|string|max:10|unique:categories',
            'type_stockage' => 'required|in:Frais,Congelé,AmbiantSec,AmbiantHumide'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        // Générer automatiquement le code_prefix si non fourni
        if (empty($data['code_prefix'])) {
            $data['code_prefix'] = $this->genererCodePrefix($request->nom);
        }

        $categorie = Categorie::create($data);

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
            'code_prefix' => 'nullable|string|max:10|unique:categories,code_prefix,' . $id,
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

    /**
     * Génère un code_prefix basé sur le nom de la catégorie
     */
    private function genererCodePrefix($nom)
    {
        // Nettoyer le nom et prendre les 3-5 premiers caractères
        $nomClean = Str::upper(Str::ascii($nom));
        $nomClean = preg_replace('/[^A-Z]/', '', $nomClean); // Garder uniquement les lettres

        // Générer le préfixe de base
        $prefixBase = substr($nomClean, 0, min(5, strlen($nomClean)));

        // Si trop court, compléter
        if (strlen($prefixBase) < 3) {
            $prefixBase = str_pad($prefixBase, 3, 'X');
        }

        // Vérifier l'unicité et ajouter un numéro si nécessaire
        $prefix = $prefixBase;
        $counter = 1;

        while (Categorie::where('code_prefix', $prefix)->exists()) {
            $prefix = $prefixBase . $counter;
            $counter++;
        }

        return $prefix;
    }
}
