<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saprodis', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('minimum_stock');
        });

        DB::table('saprodis')
            ->where('category', 'Peralatan')
            ->update(['category' => 'Peralatan Pertanian']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saprodis', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
