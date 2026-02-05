<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('slug');
            }
            
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
        
        $indexName = 'categories_is_active_sort_order_index';
        $indexExists = collect(DB::select("SHOW INDEXES FROM categories WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if (!$indexExists) {
            Schema::table('categories', function (Blueprint $table) use ($indexName) {
                $table->index(['is_active', 'sort_order'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'categories_is_active_sort_order_index';
        $indexExists = collect(DB::select("SHOW INDEXES FROM categories WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if ($indexExists) {
            Schema::table('categories', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
        
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            
            if (Schema::hasColumn('categories', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
