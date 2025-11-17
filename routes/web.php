<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(App\Http\Controllers\WebController::class)->group(function () {
    Route::get('/', 'index')->name('frontend.site');
    Route::get('/beranda', 'index')->name('frontend.beranda');
    Route::get('/verifikasi-sppd/{params}', 'verifikasi_spd')->name('frontend.verifikasi-sppd');
    Route::get('/verifikasi-std/{params}', 'verifikasi_std')->name('frontend.verifikasi-std');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/' . env('APP_FOLDER') . '/public/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get('/' . env('APP_FOLDER') . '/public/livewire/livewire.js', $handle);
});

Route::get('/login', App\Livewire\Auth\Login::class)->name('auth.login');

Route::group(['middleware' => 'isLogin'], function () {

    Route::controller(App\Http\Controllers\CetakController::class)->group(function () {
        Route::get('/cetak/sppd/{params}', 'sppd')->name('cetak.sppd');
        Route::get('/cetak/std/{params}', 'std')->name('cetak.std');
    });

    Route::get('/gantiperan/{role}', [App\Http\Controllers\ChangeRoleController::class, 'index'])->name('change.role');

    // route user admin
    Route::prefix('admin/')->group(function () {
        Route::controller(App\Http\Controllers\Admin\DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('admin.dashboard');
            Route::get('/dashboard/statistik-departemen', 'get_statistik_usulan_departemen')->name('admin.dashboard.satistik-departemen');
            Route::get('/dashboard/statistik-pegawai', 'get_statistik_usulan_pegawai')->name('admin.dashboard.satistik-pegawai');
        });

        Route::group(['middleware' => ['role:developper|admin-spd|admin-st|admin-st-dk|ppk|review-st']], function () {
            Route::controller(App\Http\Controllers\Admin\DepartemenController::class)->group(function () {
                Route::get('/departemen/index', 'index')->name('admin.departemen.index');
                Route::get('/departemen/unitkhusus/{params}', 'unitkhusus')->name('admin.departemen.unitkhusus');
                Route::get('/departemen/search-departemen', 'search_departemen')->name('admin.departemen.search-departemen');
            });

            Route::get('/pimpinan/index', [App\Http\Controllers\Admin\PimpinanController::class, 'index'])->name('admin.pimpinan.index');

            Route::controller(App\Http\Controllers\Admin\PegawaiController::class)->group(function () {
                Route::get('/pegawai/index', 'index')->name('admin.pegawai.index');
                Route::get('/pegawai/import/{params}', 'importdata')->name('admin.pegawai.import');
                Route::post('/pegawai/act-import', 'act_importdata')->name('admin.pegawai.act-import');
                Route::get('/pegawai/search-pegawai', 'search_pegawai')->name('admin.pegawai.search-pegawai');
            });

            Route::get('/kodesurat/index', [App\Http\Controllers\Admin\KodeSuratController::class, 'index'])->name('admin.kodesurat.index');

            Route::controller(App\Http\Controllers\Admin\SppdController::class)->group(function () {
                Route::get('/sppd/index', 'index')->name('admin.sppd.index');
                Route::get('/sppd/create', 'create')->name('admin.sppd.create');
                Route::get('/sppd/edit/{params}', 'edit')->name('admin.sppd.edit');
                Route::get('/sppd/delete/{params}', 'delete')->name('admin.sppd.delete');
            });

            Route::controller(App\Http\Controllers\Admin\ReviewSppdController::class)->group(function () {
                Route::get('/sppd/review', 'index')->name('admin.sppd.review');
                Route::get('/sppd/pembatalan', 'pembatalan')->name('admin.sppd.pembatalan');
            });

            Route::controller(App\Http\Controllers\Admin\LaporanSppdController::class)->group(function () {
                Route::get('/sppd/laporan', 'index')->name('admin.sppd.laporan');
                Route::get('/sppd/export/excel', 'excel')->name('admin.sppd.laporan.export.excel');
                Route::get('/sppd/export/pdf', 'pdf')->name('admin.sppd.laporan.export.pdf');
            });

            Route::controller(App\Http\Controllers\Admin\StdController::class)->group(function () {
                Route::get('/std/index', 'index')->name('admin.std.index');
                Route::get('/std/create', 'create')->name('admin.std.create');
                Route::get('/std/from-sppd', 'stdfromsppd')->name('admin.std.fromSppd');
                Route::get('/std/edit/{params}', 'edit')->name('admin.std.edit');
                Route::get('/std/lengkapi/{params}', 'lengkapi')->name('admin.std.lengkapi');
                Route::get('/std/delete/{params}', 'delete')->name('admin.std.delete');
            });

            Route::controller(App\Http\Controllers\Admin\ReviewStdController::class)->group(function () {
                Route::get('/std/review', 'index')->name('admin.std.review');
            });

            Route::controller(App\Http\Controllers\Admin\LaporanStdController::class)->group(function () {
                Route::get('/std/laporan', 'index')->name('admin.std.laporan');
                Route::get('/std/export/excel', 'excel')->name('admin.std.laporan.export.excel');
                Route::get('/std/export/pdf', 'pdf')->name('admin.std.laporan.export.pdf');
            });
        });

        Route::get('/roles/index', App\Livewire\Sistem\Roles::class)->name('admin.roles');
        Route::get('/pengguna/index', App\Livewire\Sistem\Pengguna::class)->name('admin.pengguna');
        Route::get('/referensi/index', App\Livewire\Sistem\Referensi::class)->name('admin.referensi');
    });

    // route keuangan
    Route::prefix('keuangan/')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Keuangan\DashboardController::class, 'index'])->name('keuangan.dashboard');
        Route::group(['middleware' => ['role:keuangan']], function () {
            Route::controller(App\Http\Controllers\Keuangan\SppdController::class)->group(function () {
                Route::get('/sppd/index', 'index')->name('keuangan.sppd.index');
            });
            Route::controller(App\Http\Controllers\Keuangan\StdController::class)->group(function () {
                Route::get('/std/index', 'index')->name('keuangan.std.index');
            });
        });
    });
});
