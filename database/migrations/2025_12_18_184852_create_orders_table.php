<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('restrict');
            $table->string('tracking_number')->unique()->nullable();
            $table->text('item_description')->comment('What is being shipped');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('schedule')->nullable()->comment('Shipment schedule: weekly, biweekly, monthly');
            $table->integer('duration_days')->nullable()->comment('Estimated transit time in days');
            $table->decimal('container_price', 10, 2)->comment('Snapshot of container price');
            $table->decimal('customs_fee', 10, 2)->default(0)->comment('Manually added by admin');
            $table->decimal('total_cost', 10, 2)->comment('route_price + container_price + customs_fee');
            $table->enum('status', [
                'pending_approval',
                'approved',
                'rejected',
                'awaiting_payment',
                'paid',
                'processing',
                'completed',
                'cancelled',
            ])->default('pending_approval');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
