<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToTutosTablee extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            if (!Schema::hasColumn('tutos', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}