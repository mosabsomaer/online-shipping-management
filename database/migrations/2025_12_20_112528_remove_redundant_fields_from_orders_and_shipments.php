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
        // Remove redundant fields from orders table
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['route_id']);

            // Drop columns
            $table->dropColumn([
                'route_id',
                'item_description',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained('routes')->onDelete('restrict');
            $table->text('item_description');
        });
    }
};
