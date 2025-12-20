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
        // Add route_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('route_id')->nullable()->after('merchant_id')->constrained('routes')->onDelete('restrict');
        });

        // Remove route_id from shipments table
        Schema::table('shipments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['route_id']);
            // Drop column
            $table->dropColumn('route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add route_id back to shipments table
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('route_id')->after('order_id')->constrained('routes')->onDelete('restrict');
        });

        // Remove route_id from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['route_id']);
            $table->dropColumn('route_id');
        });
    }
};
