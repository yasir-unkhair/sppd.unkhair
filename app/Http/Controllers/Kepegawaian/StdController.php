<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Exports\StdExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratTugasDinas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StdController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['role:kepegawaian']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $std_dk = 1;
            $listdata = SuratTugasDinas::with(['pegawai'])->dalam_kota($std_dk)->status_std(['200'])->tahun(tahun())
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

            return DataTables::of($listdata)
                ->addIndexColumn()
                ->editColumn('nomor_std', function ($row) {
                    $detail = "detail('" . encode_arr(['stugas_id' => $row->id]) . "')";
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_std, 70, '...')) . '</li>';
                    $str .= '<li class="list-group-item p-0"><a href="#" onclick="' . $detail . '" class="">' . $row->nomor_std . '</a></li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_berangakat', function ($row) {
                    return str_tanggal_dinas($row->tanggal_mulai_tugas, $row->tanggal_selesai_tugas);
                })
                ->editColumn('pegawai', function ($row) {
                    $str = '-';
                    if ($row->pegawai) {
                        $str = '<ul class="list-group list-group-flush">';
                        foreach ($row->pegawai as $index => $r) {
                            if (count($row->pegawai) == 1) {
                                $str .=
                                    '<li class="list-group-item p-0">' .
                                    $r->nama_pegawai .
                                    '</li>';
                                break;
                            }

                            $nomor = $index + 1;
                            if ($nomor <= 3) {
                                $str .=
                                    '<li class="list-group-item p-0">' .
                                    $nomor .
                                    '. ' .
                                    $r->nama_pegawai .
                                    '</li>';
                            } else {
                                $str .=
                                    '<li class="list-group-item p-0 text-muted">&nbsp;... </li>';
                                break;
                            }
                        }
                        $str .= '</ul>';
                    }
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
                            $w->orWhere('app_surat_tugas_dinas.nomor_std', 'LIKE', "%$search%")
                                ->orWhere('app_surat_tugas_dinas.kegiatan_std', 'LIKE', "%$search%");
                        });

                        $instance->orWhereHas('pegawai', function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->where('app_pegawai.nama_pegawai', 'LIKE', "%$search%");
                        });
                    }

                    $instance->where('app_surat_tugas_dinas.std_dk', 1);
                })
                ->rawColumns(['nomor_std', 'tanggal_berangakat', 'pegawai', 'departemen'])
                ->make(true);
        }

        $data = [
            'judul' => 'Laporan STD',
            'datatable2' => [
                'url' => route('kepegawaian.std.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_std', 'name' => 'nomor_std', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.kepegawaian.std-index', $data);
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
}
