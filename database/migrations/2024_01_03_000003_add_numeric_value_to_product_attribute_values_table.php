<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            if (! Schema::hasColumn('product_attribute_values', 'numeric_value')) {
                $table->decimal('numeric_value', 10, 2)->nullable()->after('attribute_value_id');
                $table->index('numeric_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            if (Schema::hasColumn('product_attribute_values', 'numeric_value')) {
                $table->dropIndex(['numeric_value']);
                $table->dropColumn('numeric_value');
            }
        });
    }
};
