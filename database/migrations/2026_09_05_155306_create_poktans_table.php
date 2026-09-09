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
        Schema::create('poktans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('chairperson');
            $table->string('district')->index();
            $table->string('village');
            $table->string('commodity')->index();
            $table->unsignedInteger('member_count')->default(0);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default('Aktif')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poktans');
    }
};
