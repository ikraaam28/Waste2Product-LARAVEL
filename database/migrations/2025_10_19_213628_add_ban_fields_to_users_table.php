<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBanFieldsToUsersTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // add columns only if missing
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'ban_reason')) {
                $table->text('ban_reason')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('users', 'banned_until')) {
                $table->timestamp('banned_until')->nullable()->after('ban_reason');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'banned_until')) {
                $table->dropColumn('banned_until');
            }
            if (Schema::hasColumn('users', 'ban_reason')) {
                $table->dropColumn('ban_reason');
            }
        });
    }
}