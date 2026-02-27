<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_email');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('ward')->nullable();
            $table->string('street')->nullable();
            $table->string('region');
            $table->string('city')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->unique(['user_id', 'status']); // One active application per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_applications');
    }
};
