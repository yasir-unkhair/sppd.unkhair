<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StdExport;
use App\Http\Controllers\Controller;
use App\Models\SuratTugasDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

use PDF;

class LaporanStdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:ppk']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tahun = tahun();
            $listdata = SuratTugasDinas::with(['departemen', 'pegawai'])->status_std(['200'])->tahun($tahun)
                ->select([
                    'app_surat_tugas_dinas.id',
                    'app_surat_tugas_dinas.nomor_std',
                    'app_surat_tugas_dinas.kegiatan_std',
                    'app_surat_tugas_dinas.tanggal_std',
                    'app_surat_tugas_dinas.tanggal_mulai_tugas',
                    'app_surat_tugas_dinas.tanggal_selesai_tugas',
                    'app_surat_tugas_dinas.departemen_id'
                ])
                ->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC')
                ->orderBy('app_surat_tugas_dinas.departemen_id', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('nomor_std', function ($row) {
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_std, 60, ' ...')) . '</li>';
                    $str .= '<li class="list-group-item p-0">' . $row->nomor_std . '</li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_std', function ($row) {
                    $str = tgl_indo($row->tanggal_std, false);
                    return $str;
                })
                ->editColumn('pegawai', function ($row) {
                    $str = '<ul class="list-group list-group-flush">';
                    foreach ($row->pegawai as $index => $r) {
                        $nomor = $index + 1;
                        if ($nomor <= 3) {
                            $str .= '<li class="list-group-item p-0">' . $nomor . '. ' . $r->nama_pegawai . '</li>';
                        } else {
                            $str .= '<li class="list-group-item p-0 text-muted">&nbsp;... </li>';
                            break;
                        }
                    }
                    $str .= '</ul>';
                    return  $str;
                })
                ->editColumn('departemen', function ($row) {
                    return $row->departemen->departemen ?? '-';
                })
                ->filter(function ($instance) use ($request) {
                    $filter = false;
                    if ($request->get('tanggal_awal') && $request->get('tanggal_akhir')) {
                        $tgl_mulai = Carbon::parse($request->get('tanggal_awal'))->format('Y-m-d');
                        $tgl_akhir = Carbon::parse($request->get('tanggal_akhir'))->format('Y-m-d');
                        $instance->whereBetween('app_surat_tugas_dinas.tanggal_std', [$tgl_mulai, $tgl_akhir]);
                        $filter = true;
                    }

                    if ($request->get('departemen_id')) {
                        $instance->where('app_surat_tugas_dinas.departemen_id', '=', $request->get('departemen_id'));
                        $filter = true;
                    }

                    if (!$filter) {
                        $instance->where('app_surat_tugas_dinas.nomor_std', '=', '-');
                    }

                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->where('app_surat_tugas_dinas.nomor_std', 'LIKE', "%$search%")
                                ->orWhere('app_surat_tugas_dinas.kegiatan_std', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nomor_std', 'tanggal_std', 'pegawai', 'departemen'])
                ->make(true);
        }

        $data = [
            'judul' => 'Laporan STD',
            'datatable2' => [
                'url' => route('admin.std.laporan'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_std', 'name' => 'nomor_std', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_std', 'name' => 'tanggal_std', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.admin.std.laporan', $data);
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

        $nama_file = time() . ' - Export STD.xlsx';
        $params = [
            'tgl_mulai' => trim($tanggal[0]),
            'tgl_akhir' => trim($tanggal[1]),
            'departemen_id' => $departemen_id
        ];

        return Excel::download(new StdExport($params), $nama_file);
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
        $liststd = SuratTugasDinas::with(['departemen', 'pegawai'])->status_std(['200'])->tahun(tahun())
            ->select([
                'app_surat_tugas_dinas.id',
                'app_surat_tugas_dinas.nomor_std',
                'app_surat_tugas_dinas.kegiatan_std',
                'app_surat_tugas_dinas.tanggal_std',
                'app_surat_tugas_dinas.tanggal_mulai_tugas',
                'app_surat_tugas_dinas.tanggal_selesai_tugas',
                'app_surat_tugas_dinas.departemen_id',
                'app_surat_tugas_dinas.created_at',
            ]);
        $liststd->whereBetween('app_surat_tugas_dinas.tanggal_std', [$tgl_mulai, $tgl_akhir]);

        if ($departemen_id) {
            $liststd->where('app_surat_tugas_dinas.departemen_id', '=', $departemen_id);
        }

        $liststd->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC');
        $liststd->orderBy('app_surat_tugas_dinas.departemen_id', 'ASC');

        $data = [
            'liststd' => $liststd->get(),
            'tanggal' => str_tanggal_dinas(trim($tanggal[0]), trim($tanggal[1]))
        ];
        $pdf = PDF::loadView('exports.std-pdf', $data)->setPaper('a4', 'landscape');


        $judul = date('Ymd') . ' - ' . 'Laporan Pengajuan STD.pdf';

        //menampilkan output beupa halaman PDF
        return $pdf->stream($judul);
    }
}
