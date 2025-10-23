<?php

namespace App\Exports;

use App\Models\SuratTugasDinas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class StdExport implements FromView
{
    protected $tgl_mulai;
    protected $tgl_akhir;
    protected $departemen_id;

    public function __construct(array $params)
    {
        $this->tgl_mulai = $params['tgl_mulai'];
        $this->tgl_akhir = $params['tgl_akhir'];
        $this->departemen_id = $params['departemen_id'];
    }

    public function view(): View
    {
        $tgl_mulai = Carbon::parse($this->tgl_mulai)->format('Y-m-d');
        $tgl_akhir = Carbon::parse($this->tgl_akhir)->format('Y-m-d');
        $liststd = SuratTugasDinas::with(['departemen', 'pegawai'])->status_std(['200'])->tahun(tahun())
            ->select([
                'app_surat_tugas_dinas.id',
                'app_surat_tugas_dinas.nomor_std',
                'app_surat_tugas_dinas.kegiatan_std',
                'app_surat_tugas_dinas.tanggal_std',
                'app_surat_tugas_dinas.tanggal_mulai_tugas',
                'app_surat_tugas_dinas.tanggal_selesai_tugas',
                'app_surat_tugas_dinas.departemen_id'
            ]);
        $liststd->whereBetween('app_surat_tugas_dinas.tanggal_std', [$tgl_mulai, $tgl_akhir]);

        if ($this->departemen_id) {
            $liststd->where('app_surat_tugas_dinas.departemen_id', '=', $this->departemen_id);
        }

        $liststd->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC');
        $liststd->orderBy('app_surat_tugas_dinas.departemen_id', 'ASC');

        return view('exports.std-excel', [
            'tgl_pengajuan' => str_tanggal_dinas($this->tgl_mulai, $this->tgl_akhir),
            'listsppd' => $liststd->get()
        ]);
    }
}
