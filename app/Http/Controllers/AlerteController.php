<?php

// app/Http/Controllers/AlerteController.php

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

    public function marquerLue($id)
    {
        $alerte = Alerte::findOrFail($id);
        $alerte->marquerCommeLue();

        return response()->json([
            'message' => 'Alerte marquée comme lue',
            'alerte' => $alerte
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
