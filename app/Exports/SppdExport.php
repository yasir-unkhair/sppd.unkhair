<?php

namespace App\Exports;

use App\Models\SuratPerjalananDinas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SppdExport implements FromView
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
        $tahun = tahun();
        $listsppd = SuratPerjalananDinas::with(['departemen'])->status_spd(['200'])->tahun($tahun)->join('app_pegawai AS b', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'b.id')
            ->select([
                'app_surat_perjalanan_dinas.id',
                'app_surat_perjalanan_dinas.nomor_spd',
                'app_surat_perjalanan_dinas.kegiatan_spd',
                'app_surat_perjalanan_dinas.tanggal_spd',
                'app_surat_perjalanan_dinas.tujuan',
                'app_surat_perjalanan_dinas.departemen_id',
                'app_surat_perjalanan_dinas.kode_mak',
                'app_surat_perjalanan_dinas.detail_alokasi_anggaran',
                'app_surat_perjalanan_dinas.nilai_pencairan',
                'b.nama_pegawai',
                'b.nip',
            ]);
        $listsppd->whereBetween('tanggal_spd', [$tgl_mulai, $tgl_akhir]);

        if ($this->departemen_id) {
            $listsppd->where('app_surat_perjalanan_dinas.departemen_id', '=', $this->departemen_id);
        }

        $listsppd->orderBy('app_surat_perjalanan_dinas.tanggal_spd', 'DESC');
        $listsppd->orderBy('app_surat_perjalanan_dinas.departemen_id', 'ASC');

        return view('exports.sppd-excel', [
            'tgl_pengajuan' => str_tanggal_dinas($this->tgl_mulai, $this->tgl_akhir),
            'listsppd' => $listsppd->get()
        ]);
    }
}
