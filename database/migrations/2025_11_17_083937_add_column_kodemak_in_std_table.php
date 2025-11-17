<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = 'app_surat_tugas_dinas';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumns($this->table, ['kode_mak', 'detail_alokasi_anggaran', 'nilai_pencairan'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('kode_mak');
                $table->dropColumn('detail_alokasi_anggaran');
                $table->dropColumn('nilai_pencairan');
            });
        }

        if (!Schema::hasColumns($this->table, ['kode_mak', 'detail_alokasi_anggaran', 'nilai_pencairan'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->char('kode_mak', 25)->nullable()->after('keterangan');
                $table->string('detail_alokasi_anggaran')->nullable()->after('kode_mak');
                $table->decimal('nilai_pencairan', 10, 0)->nullable()->comment('diinput oleh keuangan')->after('detail_alokasi_anggaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns($this->table, ['kode_mak', 'detail_alokasi_anggaran', 'nilai_pencairan'])) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('kode_mak');
                $table->dropColumn('detail_alokasi_anggaran');
                $table->dropColumn('nilai_pencairan');
            });
        }
    }
};
