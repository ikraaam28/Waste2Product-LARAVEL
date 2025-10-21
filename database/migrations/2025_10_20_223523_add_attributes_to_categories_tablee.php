<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesToCategoriesTablee extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('attributes')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('attributes');
        });
    }
}