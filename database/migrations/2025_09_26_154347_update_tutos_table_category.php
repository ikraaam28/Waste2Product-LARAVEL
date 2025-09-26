<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTutosTableCategory extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', ['plastic', 'wood', 'paper', 'metal', 'glass', 'other'])->change();
        });
    }

    public function down()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', ['plastique', 'bois', 'papier', 'metal', 'verre', 'autre'])->change();
        });
    }
}