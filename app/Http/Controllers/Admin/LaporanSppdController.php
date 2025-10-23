<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SppdExport;
use App\Http\Controllers\Controller;
use App\Models\SuratPerjalananDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

use PDF;

class LaporanSppdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:ppk']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tahun = tahun();
            $listdata = SuratPerjalananDinas::with(['departemen'])->status_spd(['200'])->tahun($tahun)->join('app_pegawai AS b', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'b.id')
                ->select([
                    'app_surat_perjalanan_dinas.id',
                    'app_surat_perjalanan_dinas.nomor_spd',
                    'app_surat_perjalanan_dinas.tanggal_spd',
                    'app_surat_perjalanan_dinas.tujuan',
                    'app_surat_perjalanan_dinas.departemen_id',
                    'app_surat_perjalanan_dinas.kode_mak',
                    'app_surat_perjalanan_dinas.detail_alokasi_anggaran',
                    'app_surat_perjalanan_dinas.nilai_pencairan',
                    'b.nama_pegawai',
                    'b.nip',
                ])
                ->orderBy('app_surat_perjalanan_dinas.tanggal_spd', 'DESC')
                ->orderBy('app_surat_perjalanan_dinas.departemen_id', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('nomor_spd', function ($row) {
                    return $row->nomor_spd;
                })
                ->editColumn('tanggal_berangakat', function ($row) {
                    return tgl_indo($row->tanggal_spd, false);
                })
                ->editColumn('pegawai', function ($row) {
                    $str = $row->nama_pegawai;
                    return  $str;
                })
                ->editColumn('departemen', function ($row) {
                    return $row->departemen->departemen ?? '-';
                })
                ->editColumn('nilai_pencairan', function ($row) {
                    return 'Rp. ' . rupiah($row->nilai_pencairan);
                })
                ->filter(function ($instance) use ($request) {
                    $filter = false;
                    if ($request->get('tanggal_awal') && $request->get('tanggal_akhir')) {
                        $tgl_mulai = Carbon::parse($request->get('tanggal_awal'))->format('Y-m-d');
                        $tgl_akhir = Carbon::parse($request->get('tanggal_akhir'))->format('Y-m-d');
                        $instance->whereBetween('tanggal_spd', [$tgl_mulai, $tgl_akhir]);
                        $filter = true;
                    }

                    if ($request->get('departemen_id')) {
                        $instance->where('app_surat_perjalanan_dinas.departemen_id', '=', $request->get('departemen_id'));
                        $filter = true;
                    }

                    if (!$filter) {
                        $instance->where('app_surat_perjalanan_dinas.nomor_spd', '=', '-');
                    }

                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->where('app_surat_perjalanan_dinas.nomor_spd', 'LIKE', "%$search%")
                                ->orWhere('app_surat_perjalanan_dinas.tujuan', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nomor_spd', 'tanggal_berangakat', 'pegawai', 'departemen', 'nilai_pencairan'])
                ->make(true);
        }

        $data = [
            'judul' => 'Laporan SPPD',
            'datatable2' => [
                'url' => route('admin.sppd.laporan'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_spd', 'name' => 'nomor_spd', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tujuan', 'name' => 'tujuan', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'kode_mak', 'name' => 'kode_mak', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'detail_alokasi_anggaran', 'name' => 'detail_alokasi_anggaran', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nilai_pencairan', 'name' => 'nilai_pencairan', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.admin.sppd.laporan', $data);
    }

    public function excel(Request $request)
    {
        if (!trim($request->date)) {
            abort(500);
        }
        if (trim($request->date) == 'to') {
            abort(500);
        }

        $tanggal = explode('to', $request->date);
        $departemen_id = $request->departemen_id;

        $nama_file = time() . ' - Export SPPD.xlsx';
        $params = [
            'tgl_mulai' => trim($tanggal[0]),
            'tgl_akhir' => trim($tanggal[1]),
            'departemen_id' => $departemen_id
        ];

        return Excel::download(new SppdExport($params), $nama_file);
    }

    public function pdf(Request $request)
    {
        if (!trim($request->date)) {
            abort(500);
        }
        if (trim($request->date) == 'to') {
            abort(500);
        }

        $tanggal = explode('to', $request->date);
        $departemen_id = $request->departemen_id;


        $tgl_mulai = Carbon::parse(trim($tanggal[0]))->format('Y-m-d');
        $tgl_akhir = Carbon::parse(trim($tanggal[1]))->format('Y-m-d');
        $listsppd = SuratPerjalananDinas::with(['departemen'])->status_spd(['200'])->tahun(tahun())->join('app_pegawai AS b', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'b.id')
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
                'app_surat_perjalanan_dinas.created_at',
                'b.nama_pegawai',
                'b.nip',
            ]);
        $listsppd->whereBetween('tanggal_spd', [$tgl_mulai, $tgl_akhir]);

        if ($departemen_id) {
            $listsppd->where('app_surat_perjalanan_dinas.departemen_id', '=', $departemen_id);
        }

        $listsppd->orderBy('app_surat_perjalanan_dinas.tanggal_spd', 'DESC');
        $listsppd->orderBy('app_surat_perjalanan_dinas.departemen_id', 'ASC');

        $data = [
            'listsppd' => $listsppd->get(),
            'tanggal' => str_tanggal_dinas(trim($tanggal[0]), trim($tanggal[1]))
        ];
        $pdf = PDF::loadView('exports.sppd-pdf', $data)->setPaper('legal', 'landscape');


        $judul = date('Ymd') . ' - ' . 'Laporan Pengajuan SPPD.pdf';

        //menampilkan output beupa halaman PDF
        return $pdf->stream($judul);
    }
}
