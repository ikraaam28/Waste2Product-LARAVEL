<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'supplier'])->default('user')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->text('company_name')->nullable()->after('city');
            $table->text('company_description')->nullable()->after('company_name');
            $table->string('business_license')->nullable()->after('company_description');
            $table->json('supplier_categories')->nullable()->after('business_license');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_active',
                'company_name',
                'company_description',
                'business_license',
                'supplier_categories'
            ]);
        });
    }
};
