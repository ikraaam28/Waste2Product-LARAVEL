<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuto_user_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tuto_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['like', 'dislike', 'view']);
            $table->timestamps();

            $table->unique(['user_id', 'tuto_id', 'type']); // empêche doublons
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuto_user_reactions');
    }
};
