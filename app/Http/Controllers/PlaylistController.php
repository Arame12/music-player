<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    // 💡 LISTE - GET /api/playlists
    public function index()
    {
        // Récupérer les playlists avec leurs chansons (eager loading)
        // 💡 "with('songs')" = récupère aussi les chansons de chaque playlist en une seule requête
        $playlists = Playlist::where('user_id', auth()->id())
                             ->with('songs')
                             ->get();

        return response()->json($playlists);
    }

    // 💡 CRÉER - POST /api/playlists
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $playlist = Playlist::create([
            'name'        => $request->name,
            'description' => $request->description,
            'user_id'     => auth()->id(),
        ]);

        return response()->json([
            'playlist' => $playlist,
            'message'  => 'Playlist créée !'
        ], 201);
    }

    // 💡 AJOUTER UNE CHANSON À UNE PLAYLIST - POST /api/playlists/{id}/songs
    public function addSong(Request $request, Playlist $playlist)
    {
        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'song_id' => 'required|exists:songs,id', // La chanson doit exister
            'order'   => 'nullable|integer',
        ]);

        // 💡 attach() ajoute la chanson à la playlist dans la table playlist_song
        $playlist->songs()->attach($request->song_id, [
            'order' => $request->order ?? 0
        ]);

        return response()->json(['message' => 'Chanson ajoutée à la playlist !']);
    }

    // 💡 RETIRER UNE CHANSON D'UNE PLAYLIST - DELETE /api/playlists/{id}/songs/{songId}
    public function removeSong(Playlist $playlist, $songId)
    {
        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // 💡 detach() retire la chanson de la playlist
        $playlist->songs()->detach($songId);

        return response()->json(['message' => 'Chanson retirée de la playlist !']);
    }

    // 💡 SUPPRIMER - DELETE /api/playlists/{id}
    public function destroy(Playlist $playlist)
    {
        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $playlist->delete();

        return response()->json(['message' => 'Playlist supprimée !']);
    }
}
