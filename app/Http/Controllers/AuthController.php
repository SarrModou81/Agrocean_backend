<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string',
            'role' => 'required|in:Administrateur,Commercial,GestionnaireStock,Comptable,AgentApprovisionnement'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
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

        $token = auth()->login($user);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        $user = auth()->user();

        if (!$user->is_active) {
            return response()->json(['error' => 'Compte désactivé'], 403);
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'user' => $user,
            'permissions' => $user->getPermissions(),
            'role' => $user->role
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh(),
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect'
            ], 400);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Mot de passe modifié avec succès'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20'
        ]);

        $user = Auth::user();

        if ($request->has('name')) {
            // Si vous avez nom et prenom séparés
            $nameParts = explode(' ', $request->name, 2);
            $user->prenom = $nameParts[0] ?? '';
            $user->nom = $nameParts[1] ?? '';
        }

        if ($request->has('telephone')) {
            $user->telephone = $request->telephone;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $user
        ]);
    }
}
