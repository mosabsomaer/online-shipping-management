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
        Schema::table('routes', function (Blueprint $table) {
            // Only drop old string columns if they exist
            if (Schema::hasColumn('routes', 'origin')) {
                $table->dropColumn('origin');
            }
            if (Schema::hasColumn('routes', 'destination')) {
                $table->dropColumn('destination');
            }
        });

        Schema::table('routes', function (Blueprint $table) {
            // Add new port foreign key columns if they don't exist
            if (! Schema::hasColumn('routes', 'origin_port_id')) {
                $table->foreignId('origin_port_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ports')
                    ->onDelete('restrict');
            }

            if (! Schema::hasColumn('routes', 'destination_port_id')) {
                $table->foreignId('destination_port_id')
                    ->nullable()
                    ->after('origin_port_id')
                    ->constrained('ports')
                    ->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('origin_port_id');
            $table->dropConstrainedForeignId('destination_port_id');

            // Restore old string columns
            $table->string('origin')->after('id');
            $table->string('destination')->after('origin');
        });
    }
};
