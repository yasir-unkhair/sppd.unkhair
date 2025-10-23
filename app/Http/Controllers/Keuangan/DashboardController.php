<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $tahun = tahun();
        $jml_pegawai = Pegawai::all()->count();
        $jml_departemen = Departemen::departemen(NULL)->get()->count();
        $jml_sppd = SuratPerjalananDinas::tahun($tahun)->status_spd(['200'])->get()->count();
        $jml_stugas = SuratTugasDinas::tahun($tahun)->status_std(['200'])->get()->count();

        $data = [
            'jml_pegawai' => $jml_pegawai,
            'jml_departemen' => $jml_departemen,
            'jml_sppd' => $jml_sppd,
            'jml_stugas' => $jml_stugas,
            'tahun' => $tahun
        ];

        return view('backend.keuangan.dashboard', $data);
    }
}
