<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fulfillment_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address');
            $table->string('city');
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vendor_dropoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('fulfillment_center_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'dropped_off', 'received', 'qc_in_progress', 'qc_passed', 'qc_failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('status');
        });

        Schema::create('cargo_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('fulfillment_center_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tracking_number')->unique();
            $table->enum('status', ['pending', 'in_transit', 'out_for_delivery', 'delivered', 'returned'])->default('pending');
            $table->text('destination_address');
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_shipment_id')->constrained()->onDelete('cascade');
            $table->string('event');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index('cargo_shipment_id');
        });

        Schema::create('delivery_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('fulfillment_center_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['planned', 'dispatched', 'in_progress', 'completed'])->default('planned');
            $table->date('scheduled_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('assigned_to');
            $table->index('status');
        });

        Schema::create('delivery_run_shipments', function (Blueprint $table) {
            $table->foreignId('delivery_run_id')->constrained()->onDelete('cascade');
            $table->foreignId('cargo_shipment_id')->constrained()->onDelete('cascade');

            $table->primary(['delivery_run_id', 'cargo_shipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_run_shipments');
        Schema::dropIfExists('delivery_runs');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('cargo_shipments');
        Schema::dropIfExists('vendor_dropoffs');
        Schema::dropIfExists('fulfillment_centers');
    }
};
