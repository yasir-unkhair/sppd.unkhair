<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Exports\SppdExport;
use App\Http\Controllers\Controller;
use App\Models\SuratPerjalananDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SppdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:kepegawaian']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tahun = tahun();
            $listdata = SuratPerjalananDinas::with(['departemen', 'pegawai'])->status_spd(['200'])->tahun($tahun)
                ->select([
                    'app_surat_perjalanan_dinas.id',
                    'app_surat_perjalanan_dinas.nomor_spd',
                    'app_surat_perjalanan_dinas.kegiatan_spd',
                    'app_surat_perjalanan_dinas.tanggal_spd',
                    'app_surat_perjalanan_dinas.tujuan',
                    'app_surat_perjalanan_dinas.departemen_id',
                    'app_surat_perjalanan_dinas.pegawai_id'
                ])
                ->orderBy('app_surat_perjalanan_dinas.tanggal_spd', 'DESC')
                ->orderBy('app_surat_perjalanan_dinas.departemen_id', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('nomor_spd', function ($row) {
                    $detail = "detail('" . encode_arr(['sppd_id' => $row->id]) . "')";
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_spd, 70, '...')) . '</li>';
                    $str .= '<li class="list-group-item p-0"><b><a href="#" onclick="' . $detail . '" class="">' . $row->nomor_spd . '</a></b></li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_berangakat', function ($row) {
                    return tgl_indo($row->tanggal_spd, false);
                })
                ->editColumn('pegawai', function ($row) {
                    $str = $row->pegawai->nama_pegawai ?? '-';
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
                        $instance->orWhereHas('pegawai', function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->where('app_pegawai.nama_pegawai', 'LIKE', "%$search%");
                        });
                    }

                    $instance->where('app_surat_perjalanan_dinas.status_spd', '=', '200');
                })
                ->rawColumns(['nomor_spd', 'tanggal_berangakat', 'pegawai', 'departemen'])
                ->make(true);
        }

        $data = [
            'judul' => 'Laporan SPPD',
            'datatable2' => [
                'url' => route('kepegawaian.sppd.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_spd', 'name' => 'nomor_spd', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tujuan', 'name' => 'tujuan', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.kepegawaian.sppd-index', $data);
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
}
