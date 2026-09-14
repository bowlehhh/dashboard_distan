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
        Schema::table('saprodis', function (Blueprint $table) {
            $table->foreignId('poktan_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('distributed_year')->nullable()->after('unit');
            $table->renameColumn('stock', 'quantity_distributed');
            $table->dropColumn(['category', 'minimum_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saprodis', function (Blueprint $table) {
            $table->string('category')->default('Tidak Berkategori')->after('name');
            $table->decimal('minimum_stock', 14, 2)->default(0)->after('quantity_distributed');
            $table->renameColumn('quantity_distributed', 'stock');
            $table->dropConstrainedForeignId('poktan_id');
            $table->dropColumn('distributed_year');
        });
    }
};
