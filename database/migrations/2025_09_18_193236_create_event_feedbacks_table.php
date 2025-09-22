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
        Schema::create('event_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->integer('rating')->default(0);
            $table->text('comment')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('recycled_quantity', 8, 2)->default(0);
            $table->decimal('co2_saved', 8, 2)->default(0);
            $table->integer('satisfaction_level')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_feedbacks');
    }
};
