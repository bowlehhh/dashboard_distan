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
            $table->string('condition')->default('Baik')->after('procurement_year');
        });

        DB::table('users')->where('role', 'operator')->update(['role' => 'pimpinan']);
        DB::table('users')->where('role', 'ppl')->update(['role' => 'penyuluh']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('pimpinan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'pimpinan')->update(['role' => 'operator']);
        DB::table('users')->where('role', 'penyuluh')->update(['role' => 'ppl']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->change();
        });

        Schema::table('alsintans', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
};
