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
        Schema::create('alsintans', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('brand_type');
            $table->string('inventory_number')->unique();
            $table->foreignId('poktan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('district')->index();
            $table->string('village');
            $table->unsignedSmallInteger('procurement_year');
            $table->string('condition')->default('Baik')->index();
            $table->string('usage_status')->default('Digunakan');
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alsintans');
    }
};
