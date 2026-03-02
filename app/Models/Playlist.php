<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    // 💡 Relation : Une playlist APPARTIENT À un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 💡 Relation : Une playlist a PLUSIEURS chansons
    // Playlist → Songs  (many to many)
    public function songs()
    {
        return $this->belongsToMany(Song::class)
                    ->withPivot('order')
                    ->orderBy('pivot_order'); // Trier par ordre
    }
}