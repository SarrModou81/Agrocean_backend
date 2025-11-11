<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index(Request $request)
    {
        $query = Alerte::with('produit')->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('lue')) {
            $query->where('lue', $request->lue);
        }

        $alertes = $query->paginate(20);

        return response()->json($alertes);
    }

    // NOUVELLE MÉTHODE : Compter les alertes non lues
    public function getNonLuesCount()
    {
        $count = Alerte::where('lue', false)->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function marquerLue($id)
    {
        $alerte = Alerte::findOrFail($id);
        $alerte->marquerCommeLue();

        return response()->json([
            'message' => 'Alerte marquée comme lue',
            'alerte' => $alerte
        ]);
    }

    // NOUVELLE MÉTHODE : Marquer toutes les alertes comme lues
    public function marquerToutesLues()
    {
        $updated = Alerte::where('lue', false)->update(['lue' => true]);

        return response()->json([
            'message' => 'Toutes les alertes ont été marquées comme lues',
            'count' => $updated
        ]);
    }

    public function destroy($id)
    {
        $alerte = Alerte::findOrFail($id);
        $alerte->delete();

        return response()->json([
            'message' => 'Alerte supprimée avec succès'
        ]);
    }
}
