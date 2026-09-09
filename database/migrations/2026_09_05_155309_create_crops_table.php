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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('commodity')->index();
            $table->foreignId('poktan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('district')->index();
            $table->decimal('planted_area', 12, 2)->default(0);
            $table->decimal('harvested_area', 12, 2)->default(0);
            $table->decimal('production', 14, 2)->default(0);
            $table->string('unit')->default('Ton');
            $table->string('period')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
