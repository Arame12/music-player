<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index()
    {
        $songs = Song::where('user_id', auth()->id())->get();

        $songs->transform(function ($song) {
            $song->audio_url = $song->audio_file ? url($song->audio_file) : null;
            $song->cover_url = $song->cover_image ? url($song->cover_image) : null;
            return $song;
        });

        return response()->json($songs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'artist'      => 'nullable|string|max:255',
            'album'       => 'nullable|string|max:255',
            'audio_file'  => 'required|file',
            'cover_image' => 'nullable|file',
        ]);

        $file     = $request->file('audio_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/audio'), $filename);
        $audioPath = 'uploads/audio/' . $filename;

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverFile     = $request->file('cover_image');
            $coverFilename = time() . '_' . $coverFile->getClientOriginalName();
            $coverFile->move(public_path('uploads/covers'), $coverFilename);
            $coverPath = 'uploads/covers/' . $coverFilename;
        }

        $song = Song::create([
            'title'       => $request->title,
            'artist'      => $request->artist,
            'album'       => $request->album,
            'audio_file'  => $audioPath,
            'cover_image' => $coverPath,
            'user_id'     => auth()->id(),
        ]);

        $song->audio_url = url($audioPath);
        $song->cover_url = $coverPath ? url($coverPath) : null;

        return response()->json([
            'song'    => $song,
            'message' => 'Chanson uploadée avec succès !'
        ], 201);
    }

    public function show(Song $song)
    {
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $song->audio_url = url($song->audio_file);
        $song->cover_url = $song->cover_image ? url($song->cover_image) : null;

        return response()->json($song);
    }

    public function update(Request $request, Song $song)
    {
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'title'  => 'sometimes|string|max:255',
            'artist' => 'nullable|string|max:255',
            'album'  => 'nullable|string|max:255',
        ]);

        $song->update($request->only(['title', 'artist', 'album']));

        return response()->json([
            'song'    => $song,
            'message' => 'Chanson modifiée avec succès !'
        ]);
    }

    public function destroy(Song $song)
    {
        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $audioPath = public_path($song->audio_file);
        if (file_exists($audioPath)) {
            unlink($audioPath);
        }

        if ($song->cover_image) {
            $coverPath = public_path($song->cover_image);
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }
        }

        $song->delete();
        return response()->json(['message' => 'Chanson supprimee !']);
    }
}