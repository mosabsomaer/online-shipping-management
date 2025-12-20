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
        Schema::table('shipments', function (Blueprint $table) {
            // Drop the unique constraint on order_id to allow multiple shipments per order
            $table->dropUnique(['order_id']);

            // Add shipment detail fields
            $table->text('item_description')->after('container_id');

            // Add pricing fields to shipments
            $table->decimal('container_price', 10, 2)->after('item_description');
            $table->decimal('customs_fee', 10, 2)->default(0)->after('container_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Remove all added fields
            $table->dropColumn([
                'item_description',
                'container_price',
                'customs_fee',
            ]);

            // Re-add unique constraint on order_id
            $table->unique('order_id');
        });
    }
};
