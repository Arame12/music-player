<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 💡 INSCRIPTION - POST /api/register
    // Quand quelqu'un crée un compte
    public function register(Request $request)
    {
        // Vérifier que les données envoyées sont correctes
        $request->validate([
            'name'     => 'required|string|max:255',     // Nom obligatoire
            'email'    => 'required|email|unique:users',  // Email unique obligatoire
            'password' => 'required|min:6|confirmed',     // Mot de passe min 6 caractères
        ]);

        // Créer l'utilisateur dans la base de données
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // 💡 Hash = chiffrer le mot de passe (sécurité !)
        ]);

        // Créer un token (clé d'accès) pour cet utilisateur
        // 💡 Le token c'est comme un badge d'accès. React va le garder et l'envoyer à chaque requête
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
            'message' => 'Compte créé avec succès !'
        ], 201); // 201 = "Créé avec succès"
    }

    // 💡 CONNEXION - POST /api/login
    // Quand quelqu'un se connecte
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Chercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur existe ET si le mot de passe est correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            // 💡 Hash::check compare le mot de passe entré avec celui chiffré dans la base
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401); // 401 = "Non autorisé"
        }

        // Créer un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
            'message' => 'Connexion réussie !'
        ]);
    }

    // 💡 DÉCONNEXION - POST /api/logout
    // Quand quelqu'un se déconnecte
    public function logout(Request $request)
    {
        // Supprimer le token actuel (invalider le badge d'accès)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie !'
        ]);
    }

    // 💡 PROFIL - GET /api/user
    // Récupérer les infos de l'utilisateur connecté
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}