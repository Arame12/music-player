<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();                              // Numéro unique automatique
            $table->string('title');                   // Titre de la chanson
            $table->string('artist')->nullable();      // Artiste (pas obligatoire)
            $table->string('album')->nullable();       // Album (pas obligatoire)
            $table->string('audio_file');              // Chemin du fichier MP3
            $table->string('cover_image')->nullable(); // Chemin de l'image
            $table->integer('duration')->nullable();   // Durée en secondes
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a uploadé
            $table->timestamps();                      // Date création/modification
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};