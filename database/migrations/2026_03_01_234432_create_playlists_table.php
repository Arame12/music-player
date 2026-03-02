<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();                               // Numéro unique automatique
            $table->string('name');                     // Nom de la playlist
            $table->text('description')->nullable();    // Description (pas obligatoire)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Propriétaire
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};