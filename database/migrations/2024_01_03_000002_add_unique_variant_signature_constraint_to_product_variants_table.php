<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexName = 'product_variants_product_id_variant_signature_unique';
        $indexExists = collect(DB::select("SHOW INDEXES FROM product_variants WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if (!$indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->unique(['product_id', 'variant_signature'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'product_variants_product_id_variant_signature_unique';
        $indexExists = collect(DB::select("SHOW INDEXES FROM product_variants WHERE Key_name = ?", [$indexName]))->isNotEmpty();
        
        if ($indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }
    }
};
