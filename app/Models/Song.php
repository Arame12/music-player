<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    // 💡 $fillable = liste des colonnes qu'on a le droit de remplir
    // C'est une sécurité pour éviter que quelqu'un envoie des données malveillantes
    protected $fillable = [
        'title',
        'artist',
        'album',
        'audio_file',
        'cover_image',
        'duration',
        'user_id'
    ];

    // 💡 Relation : Une chanson APPARTIENT À un utilisateur
    // Song → User  (many to one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 💡 Relation : Une chanson peut être dans PLUSIEURS playlists
    // Song → Playlists  (many to many)
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)
                    ->withPivot('order'); // On veut aussi récupérer la colonne "order"
    }
}