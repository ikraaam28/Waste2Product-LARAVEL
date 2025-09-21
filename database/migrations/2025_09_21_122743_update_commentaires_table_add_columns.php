<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->text('contenu')->after('id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('contenu');
            $table->foreignId('publication_id')->constrained()->onDelete('cascade')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropColumn(['contenu']);
            $table->dropForeign(['user_id', 'publication_id']);
            $table->dropColumn(['user_id', 'publication_id']);
        });
    }
};