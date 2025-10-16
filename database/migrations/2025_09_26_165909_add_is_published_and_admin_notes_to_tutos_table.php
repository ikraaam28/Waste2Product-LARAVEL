<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPublishedAndAdminNotesToTutosTable extends Migration
{
    public function up()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('user_id');
            $table->text('admin_notes')->nullable()->after('is_published');
        });
    }

    public function down()
    {
        Schema::table('tutos', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'admin_notes']);
        });
    }
}