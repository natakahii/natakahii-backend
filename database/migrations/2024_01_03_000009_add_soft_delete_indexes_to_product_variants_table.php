<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexName = 'product_variants_status_deleted_at_index';
        $indexExists = collect(DB::select("SHOW INDEXES FROM product_variants WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if (!$indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->index(['status', 'deleted_at'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'product_variants_status_deleted_at_index';
        $indexExists = collect(DB::select("SHOW INDEXES FROM product_variants WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if ($indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
