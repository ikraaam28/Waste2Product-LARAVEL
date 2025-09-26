<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaContentToTutosTable extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->binary('media_content')->nullable()->after('media');
        });
    }

    public function down()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->dropColumn('media_content');
        });
    }
}