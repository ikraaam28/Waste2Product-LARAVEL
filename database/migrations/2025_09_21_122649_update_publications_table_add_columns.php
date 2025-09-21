<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->string('titre')->after('id');
            $table->text('contenu')->after('titre');
            $table->enum('categorie', ['reemploi', 'reparation', 'transformation'])->nullable()->after('contenu');
            $table->string('image')->nullable()->after('categorie');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['titre', 'contenu', 'categorie', 'image']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};