<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['text', 'number', 'color', 'select']);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_variant_attribute')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('code');
            $table->index(['is_filterable', 'is_variant_attribute']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
