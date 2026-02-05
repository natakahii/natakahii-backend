<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'variant_signature')) {
                $table->string('variant_signature', 191)->nullable()->after('product_id');
                $table->index('variant_signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'variant_signature')) {
                $table->dropIndex(['variant_signature']);
                $table->dropColumn('variant_signature');
            }
        });
    }
};
