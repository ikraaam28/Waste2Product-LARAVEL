<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', [
                'plastique', 'bois', 'papier', 'metal', 'verre', 'autre',
                'plastic', 'wood', 'paper', 'glass', 'other'
            ])->change();
        });

        // Step 2: Update existing French values to English
        DB::statement("
            UPDATE `tutos` SET `category` = CASE
                WHEN `category` = 'plastique' THEN 'plastic'
                WHEN `category` = 'bois' THEN 'wood'
                WHEN `category` = 'papier' THEN 'paper'
                WHEN `category` = 'verre' THEN 'glass'
                WHEN `category` = 'autre' THEN 'other'
                ELSE `category`
            END
        ");

        // Step 3: Restrict ENUM to only English values
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', ['plastic', 'wood', 'paper', 'metal', 'glass', 'other'])->change();
        });
    }

    public function down()
    {
        // Step 1: Temporarily allow both English and French values
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', [
                'plastic', 'wood', 'paper', 'metal', 'glass', 'other',
                'plastique', 'bois', 'papier', 'verre', 'autre'
            ])->change();
        });

        // Step 2: Update English values back to French
        DB::statement("
            UPDATE `tutos` SET `category` = CASE
                WHEN `category` = 'plastic' THEN 'plastique'
                WHEN `category` = 'wood' THEN 'bois'
                WHEN `category` = 'paper' THEN 'papier'
                WHEN `category` = 'glass' THEN 'verre'
                WHEN `category` = 'other' THEN 'autre'
                ELSE `category`
            END
        ");

        // Step 3: Restrict ENUM to only French values
        Schema::table('tutos', function (Blueprint $table) {
            $table->enum('category', ['plastique', 'bois', 'papier', 'metal', 'verre', 'autre'])->change();
        });
    }
};