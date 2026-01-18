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
        // Variant types like "Color", "Size", "Material"
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Variant values like "Red", "Blue", "S", "M", "L", "XL"
        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained('variant_types')->onDelete('cascade');
            $table->string('value');
            $table->string('color_code')->nullable(); // For color swatches
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_values');
        Schema::dropIfExists('variant_types');
    }
};
