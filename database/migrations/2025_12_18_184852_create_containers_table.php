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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., "20ft Standard", "40ft High Cube"');
            $table->decimal('size', 10, 2)->comment('Size in m³');
            $table->decimal('price', 10, 2)->comment('Container price');
            $table->decimal('weight_limit', 10, 2)->comment('Maximum weight capacity in kg');
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
