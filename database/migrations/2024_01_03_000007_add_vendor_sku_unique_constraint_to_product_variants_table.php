<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexName = 'product_variants_vendor_id_sku_unique';
        $indexExists = collect(DB::select('SHOW INDEXES FROM product_variants WHERE Key_name = ?', [$indexName]))->isNotEmpty();

        if (! $indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->dropUnique(['sku']);
                $table->unique(['vendor_id', 'sku'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'product_variants_vendor_id_sku_unique';
        $indexExists = collect(DB::select('SHOW INDEXES FROM product_variants WHERE Key_name = ?', [$indexName]))->isNotEmpty();

        if ($indexExists) {
            Schema::table('product_variants', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
                $table->unique('sku');
            });
        }
    }
};
