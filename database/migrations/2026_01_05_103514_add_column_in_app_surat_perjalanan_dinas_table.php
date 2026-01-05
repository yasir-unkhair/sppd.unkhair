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
        if (Schema::hasColumns($this->table, ['instansi', 'sumber_dana'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('instansi');
                $table->dropColumn('sumber_dana');
            });
        }

        if (!Schema::hasColumns($this->table, ['instansi', 'sumber_dana'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->string('instansi')->nullable()->after('detail_alokasi_anggaran');
                $table->string('sumber_dana')->nullable()->after('instansi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns($this->table, ['instansi', 'sumber_dana'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('instansi');
                $table->dropColumn('sumber_dana');
            });
        }
    }
};
