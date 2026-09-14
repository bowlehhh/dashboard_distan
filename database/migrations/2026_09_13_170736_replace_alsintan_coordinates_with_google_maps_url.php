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
        Schema::table('alsintans', function (Blueprint $table) {
            $table->string('google_maps_url', 2048)->nullable()->after('photo_path');
        });

        DB::table('alsintans')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->chunkById(100, function ($alsintans): void {
                foreach ($alsintans as $alsintan) {
                    DB::table('alsintans')
                        ->where('id', $alsintan->id)
                        ->update(['google_maps_url' => 'https://www.google.com/maps?q='.rawurlencode($alsintan->latitude.','.$alsintan->longitude)]);
                }
            });

        Schema::table('alsintans', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alsintans', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dropColumn('google_maps_url');
        });
    }
};
