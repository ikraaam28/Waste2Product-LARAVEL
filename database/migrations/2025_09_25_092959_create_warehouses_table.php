<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Tunisia');
            $table->decimal('capacity', 10, 2)->default(0); // en m³ ou unités
            $table->decimal('current_occupancy', 10, 2)->default(0);
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();

            // Index
            $table->index(['partner_id', 'status']);
            $table->index('city');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
};