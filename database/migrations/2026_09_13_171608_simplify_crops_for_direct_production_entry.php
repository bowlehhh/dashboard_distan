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
        Schema::table('crops', function (Blueprint $table) {
            $table->string('production_text', 100)->nullable()->after('harvested_area');
        });

        DB::table('crops')
            ->orderBy('id')
            ->chunkById(100, function ($crops): void {
                foreach ($crops as $crop) {
                    $production = rtrim(rtrim(number_format((float) $crop->production, 2, '.', ''), '0'), '.');

                    DB::table('crops')
                        ->where('id', $crop->id)
                        ->update(['production_text' => trim($production.' '.$crop->unit)]);
                }
            });

        Schema::table('crops', function (Blueprint $table) {
            $table->dropConstrainedForeignId('poktan_id');
            $table->dropColumn(['production', 'unit']);
            $table->renameColumn('production_text', 'production');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->foreignId('poktan_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('production_number', 14, 2)->default(0);
            $table->string('unit')->default('Ton');
            $table->dropColumn('production');
            $table->renameColumn('production_number', 'production');
        });
    }
};
