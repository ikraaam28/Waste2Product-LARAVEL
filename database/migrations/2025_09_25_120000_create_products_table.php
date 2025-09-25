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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // Prix barré
            $table->string('sku')->unique()->nullable(); // Code produit
            $table->integer('stock_quantity')->default(0);
            $table->boolean('manage_stock')->default(true);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('images')->nullable(); // Tableau d'images
            $table->json('gallery')->nullable(); // Galerie d'images supplémentaires
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions')->nullable(); // LxWxH
            $table->json('attributes')->nullable(); // Attributs personnalisés
            $table->text('materials')->nullable(); // Matériaux utilisés
            $table->text('recycling_process')->nullable(); // Processus de recyclage
            $table->integer('environmental_impact_score')->nullable(); // Score impact environnemental (1-100)
            $table->json('certifications')->nullable(); // Certifications écologiques
            $table->json('tags')->nullable(); // Tags pour recherche
            $table->json('meta_data')->nullable(); // SEO et autres métadonnées
            $table->integer('views_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['is_active', 'published_at']);
            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('stock_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
