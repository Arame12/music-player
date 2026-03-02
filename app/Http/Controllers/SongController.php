<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    // 💡 LISTE - GET /api/songs
    // Récupérer toutes les chansons de l'utilisateur connecté
    public function index()
    {
        // On récupère SEULEMENT les chansons de l'utilisateur connecté
        $songs = Song::where('user_id', auth()->id())->get();

        return response()->json($songs);
    }

    // 💡 UPLOAD - POST /api/songs
    // Ajouter une nouvelle chanson
    public function store(Request $request)
    {
        // Valider les données reçues
        $request->validate([
            'title'       => 'required|string|max:255',
            'artist'      => 'nullable|string|max:255',
            'album'       => 'nullable|string|max:255',
            'audio_file'  => 'required|file|mimes:mp3,wav,ogg|max:20480', // Max 20MB
            'cover_image' => 'nullable|image|max:2048',                    // Max 2MB
        ]);

        // 💡 Sauvegarder le fichier audio dans storage/app/public/audio/
        $audioPath = $request->file('audio_file')->store('audio', 'public');

        // Sauvegarder l'image si elle existe
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        // Créer la chanson dans la base de données
        $song = Song::create([
            'title'       => $request->title,
            'artist'      => $request->artist,
            'album'       => $request->album,
            'audio_file'  => $audioPath,
            'cover_image' => $coverPath,
            'user_id'     => auth()->id(), // 💡 auth()->id() = l'ID de l'utilisateur connecté
        ]);

        return response()->json([
            'song'    => $song,
            'message' => 'Chanson uploadée avec succès !'
        ], 201);
    }

    // 💡 DÉTAIL - GET /api/songs/{id}
    // Récupérer une chanson spécifique
    public function show(Song $song)
    {
        // Vérifier que la chanson appartient à l'utilisateur connecté
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($song);
    }

    // 💡 MODIFIER - PUT /api/songs/{id}
    // Modifier une chanson
    public function update(Request $request, Song $song)
    {
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'title'  => 'sometimes|string|max:255', // "sometimes" = seulement si envoyé
            'artist' => 'nullable|string|max:255',
            'album'  => 'nullable|string|max:255',
        ]);

        $song->update($request->only(['title', 'artist', 'album']));

        return response()->json([
            'song'    => $song,
            'message' => 'Chanson modifiée avec succès !'
        ]);
    }

    // 💡 SUPPRIMER - DELETE /api/songs/{id}
    // Supprimer une chanson
    public function destroy(Song $song)
    {
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer les fichiers du serveur
        Storage::disk('public')->delete($song->audio_file);
        if ($song->cover_image) {
            Storage::disk('public')->delete($song->cover_image);
        }

        $song->delete();

        return response()->json(['message' => 'Chanson supprimée !']);
    }
}