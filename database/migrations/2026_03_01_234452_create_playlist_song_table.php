<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_song', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->onDelete('cascade'); // Quelle playlist
            $table->foreignId('song_id')->constrained()->onDelete('cascade');     // Quelle chanson
            $table->integer('order')->default(0); // Position dans la playlist
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_song');
    }
};