<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjalananDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SppdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin-spd|ppk']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // NULL maka data sppd akan tampil semua
            $tahun = tahun();
            $admin_spd = NULL;

            // menampilkan data sppd hanya punya admin-spd
            // if (session('role') == 'admin-spd') {
            //     $admin_spd = auth()->user()->id;
            // }

            $status_spd = [
                '102',
                '406',
                '200',
                '409'
            ];

            $listdata = SuratPerjalananDinas::with(['departemen', 'surat_tugas'])
                ->status_spd($status_spd)
                ->tahun($tahun)
                ->admin_spd($admin_spd)
                ->join('app_pegawai AS b', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'b.id')
                ->select([
                    'app_surat_perjalanan_dinas.id',
                    DB::raw("SUBSTRING_INDEX(app_surat_perjalanan_dinas.nomor_spd, '/', 1) AS nomor"),
                    'app_surat_perjalanan_dinas.nomor_spd',
                    'app_surat_perjalanan_dinas.tanggal_spd',
                    'app_surat_perjalanan_dinas.tujuan',
                    'app_surat_perjalanan_dinas.status_spd',
                    'app_surat_perjalanan_dinas.departemen_id',
                    'b.nama_pegawai',
                    'b.nip',
                ])
                // ->orderByRaw("FIELD(status_spd , '102', '200', '406') ASC")
                ->orderBy('nomor', 'DESC')
                ->orderBy('app_surat_perjalanan_dinas.tanggal_spd', 'DESC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $btnPrint = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default"><i class="fa fa-print"></i></button>
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>';
                    if ($row->status_spd == '200') {
                        $btnPrint .= '<div class="dropdown-menu" role="menu">';
                        $btnPrint .= '
                            <a class="dropdown-item" href="' . route('cetak.sppd', encode_arr(['sppd_id' => $row->id])) . '" target="_blank">Cetak SPPD</a>
                        ';

                        if ($row->surat_tugas?->status_std == '200') {
                            $btnPrint .= '
                                <a class="dropdown-item" href="' . route('cetak.std', encode_arr(['stugas_id' => $row->surat_tugas?->id])) . '" target="_blank">Cetak STD</a>
                            ';
                        }

                        $btnPrint .= '</div>';
                    }
                    $btnPrint .= '</div>';

                    $edit = "edit('" . encode_arr(['sppd_id' => $row->id]) . "')";
                    $btnEdit = '<button type="button" onclick="' . $edit . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button>';

                    $detail = "detail('" . encode_arr(['sppd_id' => $row->id]) . "')";
                    $confirm = "return confirm('Apakah Anda Yakin Menghapus Data?');";
                    $actionBtn = '
                    <center>
                        <button type="button" onclick="' . $detail . '" class="btn btn-sm btn-info"><i class="fa fa-info-circle"></i></button>
                        ' . $btnEdit . '
                        ' . $btnPrint . '   
                        <!-- 
                        <a href="' . route('admin.sppd.delete', encode_arr(['sppd_id' => $row->id])) . '" onclick="' . $confirm . '" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                        -->
                    </center>';
                    return $actionBtn;
                })
                ->editColumn('nomor_spd', function ($row) {
                    $detail = "detail('" . encode_arr(['sppd_id' => $row->id]) . "')";
                    return '<a href="#" onclick="' . $detail . '" class="">' . $row->nomor_spd . '</a>';
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
                ->editColumn('status', function ($row) {
                    return str_status_sppd($row->status_spd);
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('app_surat_perjalanan_dinas.nomor_spd', 'LIKE', "%$search%")
                                ->orWhere('app_surat_perjalanan_dinas.tanggal_berangakat', 'LIKE', "%$search%")
                                ->orWhere('b.nama_pegawai', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nomor_spd', 'pegawai', 'tanggal_berangakat', 'departemen', 'status', 'action'])
                ->make(true);
        }

        $data = [
            'judul' => 'Data SPPD',
            'datatable' => [
                'url' => route('admin.sppd.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_spd', 'name' => 'nomor_spd', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tujuan', 'name' => 'tujuan', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'status', 'name' => 'status', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.admin.sppd.index', $data);
    }

    public function create()
    {
        $data = [
            'judul' => 'Buat SPPD',
        ];

        return view('backend.admin.sppd.create', $data);
    }

    public function edit($params)
    {
        $data = [
            'judul' => 'Edit SPPD',
            'params' => $params
        ];

        return view('backend.admin.sppd.edit', $data);
    }

    public function delete($params)
    {
        $sppd_id = data_params($params, 'sppd_id');
        SuratPerjalananDinas::where('id', $sppd_id)->update(['status_spd' => '204']);
        alert()->success('Success', 'Data SPPD Berhasil Dihapus');
        return redirect(route('admin.sppd.index'));
    }
}
