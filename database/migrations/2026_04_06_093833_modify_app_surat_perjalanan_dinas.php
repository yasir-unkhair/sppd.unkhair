<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = 'app_surat_perjalanan_dinas';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumns($this->table, ['tamu', 'ppk_tamu'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('tamu');
                $table->dropColumn('ppk_tamu');
            });
        }

        if (!Schema::hasColumns($this->table, ['tamu', 'ppk_tamu'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->boolean('tamu')->default(0)->after('alasan')->comment('sppd khusus tamu');
                $table->string('ppk_tamu')->nullable()->after('tamu');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns($this->table, ['tamu', 'ppk_tamu'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('tamu');
                $table->dropColumn('ppk_tamu');
            });
        }
    }
};
