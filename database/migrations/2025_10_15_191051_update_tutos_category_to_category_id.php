<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;
   use App\Models\Category;
   use App\Models\Tuto;

   class UpdateTutosCategoryToCategoryId extends Migration
   {
       public function up()
       {
           // Add temporary category_id column
           Schema::table('tutos', function (Blueprint $table) {
               $table->foreignId('category_id')->nullable()->after('description')->constrained()->onDelete('cascade');
           });

           // Map existing categories to category_id
           $categoryMap = [
               'plastique' => Category::where('slug', 'plastique')->first()->id ?? null,
               'bois' => Category::where('slug', 'bois')->first()->id ?? null,
               'papier' => Category::where('slug', 'papier')->first()->id ?? null,
               'metal' => Category::where('slug', 'metal')->first()->id ?? null,
               'verre' => Category::where('slug', 'verre')->first()->id ?? null,
               'autre' => Category::where('slug', 'autre')->first()->id ?? null,
           ];

           foreach ($categoryMap as $oldCategory => $categoryId) {
               if ($categoryId) {
                   Tuto::where('category', $oldCategory)->update(['category_id' => $categoryId]);
               }
           }

           // Drop the old category column
           Schema::table('tutos', function (Blueprint $table) {
               $table->dropColumn('category');
           });
       }

       public function down()
       {
           // Revert by adding back the category enum column
           Schema::table('tutos', function (Blueprint $table) {
               $table->enum('category', ['plastique', 'bois', 'papier', 'metal', 'verre', 'autre'])->after('description');
           });

           // Map category_id back to category
           $categoryMap = [
               Category::where('slug', 'plastique')->first()->id ?? null => 'plastique',
               Category::where('slug', 'bois')->first()->id ?? null => 'bois',
               Category::where('slug', 'papier')->first()->id ?? null => 'papier',
               Category::where('slug', 'metal')->first()->id ?? null => 'metal',
               Category::where('slug', 'verre')->first()->id ?? null => 'verre',
               Category::where('slug', 'autre')->first()->id ?? null => 'autre',
           ];

           foreach ($categoryMap as $categoryId => $oldCategory) {
               if ($categoryId) {
                   Tuto::where('category_id', $categoryId)->update(['category' => $oldCategory]);
               }
           }

           // Drop the category_id column
           Schema::table('tutos', function (Blueprint $table) {
               $table->dropForeign(['category_id']);
               $table->dropColumn('category_id');
           });
       }
   }