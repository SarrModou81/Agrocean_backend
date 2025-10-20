<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtrer par rôle
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filtrer par statut
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Recherche
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('prenom', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:Administrateur,Commercial,GestionnaireStock,Comptable,AgentApprovisionnement'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role' => $request->role,
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }

    /**
     * Afficher un utilisateur
     */
    public function show($id)
    {
        $user = User::with(['ventes', 'commandeAchats'])->findOrFail($id);

        // Statistiques
        $user->statistiques = [
            'total_ventes' => $user->ventes()->count(),
            'ca_genere' => $user->ventes()->where('statut', '!=', 'Annulée')->sum('montant_ttc'),
            'total_commandes_achat' => $user->commandeAchats()->count()
        ];

        return response()->json($user);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'telephone' => 'nullable|string|max:20',
            'role' => 'sometimes|in:Administrateur,Commercial,GestionnaireStock,Comptable,AgentApprovisionnement',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $dataToUpdate = $request->only(['nom', 'prenom', 'email', 'telephone', 'role', 'is_active']);

        if ($request->has('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Ne pas supprimer l'utilisateur connecté
        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 400);
        }

        // Vérifier s'il a des ventes ou commandes
        if ($user->ventes()->count() > 0 || $user->commandeAchats()->count() > 0) {
            return response()->json([
                'error' => 'Impossible de supprimer un utilisateur ayant des ventes ou commandes',
                'suggestion' => 'Désactivez le compte au lieu de le supprimer'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'Vous ne pouvez pas désactiver votre propre compte'
            ], 400);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'Utilisateur activé' : 'Utilisateur désactivé',
            'user' => $user
        ]);
    }

    /**
     * Changer le rôle d'un utilisateur
     */
    public function assignRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:Administrateur,Commercial,GestionnaireStock,Comptable,AgentApprovisionnement'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Rôle assigné avec succès',
            'user' => $user
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès'
        ]);
    }

    /**
     * Statistiques des utilisateurs par rôle
     */
    public function statistiques()
    {
        $stats = [
            'total' => User::count(),
            'actifs' => User::where('is_active', true)->count(),
            'inactifs' => User::where('is_active', false)->count(),
            'par_role' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->get()
                ->pluck('count', 'role')
        ];

        return response()->json($stats);
    }
}
