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
        // Ensure category_id exists (idempotent)
        if (!Schema::hasColumn('tutos', 'category_id')) {
            Schema::table('tutos', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('title');
            });

            if (Schema::hasTable('categories')) {
                Schema::table('tutos', function (Blueprint $table) {
                    // attempt to add FK; if categories table exists this is safe
                    $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
                });
            }
        }

        // Only migrate old enum/string 'category' values when column exists
        if (Schema::hasColumn('tutos', 'category') && Schema::hasTable('categories')) {
            $slugs = ['plastique', 'bois', 'papier', 'metal', 'verre', 'autre'];

            foreach ($slugs as $old) {
                $cat = Category::where('slug', $old)->first();
                $categoryId = $cat ? $cat->id : null;
                if ($categoryId) {
                    Tuto::where('category', $old)->update(['category_id' => $categoryId]);
                }
            }

            // Drop the old category column only if it exists
            Schema::table('tutos', function (Blueprint $table) {
                if (Schema::hasColumn('tutos', 'category')) {
                    $table->dropColumn('category');
                }
            });
        }
    }

    public function down()
    {
        // Add back enum column if missing
        if (!Schema::hasColumn('tutos', 'category')) {
            Schema::table('tutos', function (Blueprint $table) {
                $table->enum('category', ['plastique', 'bois', 'papier', 'metal', 'verre', 'autre'])->after('description');
            });
        }

        // Map category_id back to category when possible
        if (Schema::hasColumn('tutos', 'category_id') && Schema::hasTable('categories')) {
            $categories = Category::pluck('slug', 'id')->toArray(); // [id => slug]

            foreach ($categories as $id => $slug) {
                if ($slug) {
                    Tuto::where('category_id', $id)->update(['category' => $slug]);
                }
            }

            // Drop foreign + column if present (wrap in try to avoid errors)
            try {
                Schema::table('tutos', function (Blueprint $table) {
                    if (Schema::hasColumn('tutos', 'category_id')) {
                        $table->dropForeign(['category_id']);
                        $table->dropColumn('category_id');
                    }
                });
            } catch (\Throwable $e) {
                // Best-effort: try to drop column only
                try {
                    Schema::table('tutos', function (Blueprint $table) {
                        if (Schema::hasColumn('tutos', 'category_id')) {
                            $table->dropColumn('category_id');
                        }
                    });
                } catch (\Throwable $e) {
                    // ignore — migration rollback best-effort
                }
            }
        }
    }
}