<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStepsToTutosTable extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            if (!Schema::hasColumn('tutos', 'steps')) {
                $table->json('steps')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('tutos', function (Blueprint $table) {
            if (Schema::hasColumn('tutos', 'steps')) {
                $table->dropColumn('steps');
            }
        });
    }
}
