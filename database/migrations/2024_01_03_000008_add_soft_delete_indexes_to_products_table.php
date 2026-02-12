<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexName = 'products_status_deleted_at_index';
        $indexExists = collect(DB::select('SHOW INDEXES FROM products WHERE Key_name = ?', [$indexName]))->isNotEmpty();

        if (! $indexExists) {
            Schema::table('products', function (Blueprint $table) use ($indexName) {
                $table->index(['status', 'deleted_at'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'products_status_deleted_at_index';
        $indexExists = collect(DB::select('SHOW INDEXES FROM products WHERE Key_name = ?', [$indexName]))->isNotEmpty();

        if ($indexExists) {
            Schema::table('products', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
