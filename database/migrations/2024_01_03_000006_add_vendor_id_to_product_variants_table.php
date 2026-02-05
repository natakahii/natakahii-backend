<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('vendor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropIndex(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });
    }
};
