<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PengajuanCutiController;
use App\Http\Controllers\KepalaUnitController;
use App\Http\Controllers\BiometricAllController;
use App\Http\Controllers\KuesionerController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('auth/login_v');
// });

Route::group(['name' => 'auth'], function () {
    Route::get('/', [AuthController::class, 'index'])->name('auth.login_v');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.admin_v');
    Route::get('/kepalaunit', [KepalaUnitController::class, 'index'])->name('kepalaunit.kepalaunit_v');
    Route::group(['prefix' => 'admin', 'middleware' => ['is_admin']], function () {

        Route::prefix('jadwal-satpam')->name('admin.jadwal-satpam.')->group(function () {
            Route::get('/list', [\App\Http\Controllers\JadwalSatpamController::class, 'list'])->name('list');
            Route::get('/', [\App\Http\Controllers\JadwalSatpamController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\JadwalSatpamController::class, 'store'])->name('store');
            Route::get('/calendar', [\App\Http\Controllers\JadwalSatpamController::class, 'allDataCalendar'])->name('calendar.all');
            Route::get('/calendar-by-user/{id}', [\App\Http\Controllers\JadwalSatpamController::class, 'showDataCalendarByUser'])->name('calendar.by-user');
            Route::delete('/delete/{id}', [\App\Http\Controllers\JadwalSatpamController::class, 'destroy'])->name('showDataById');
            Route::get('/by-id/{id}', [\App\Http\Controllers\JadwalSatpamController::class, 'showDataById'])->name('showDataById');
            Route::get('/by-user/{nip}', [\App\Http\Controllers\JadwalSatpamController::class, 'showByUser'])->name('showByUser');
            Route::post('/by-user/{nip}', [\App\Http\Controllers\JadwalSatpamController::class, 'storeByUser'])->name('storeByUser');
            Route::get('/by-date/off', [\App\Http\Controllers\JadwalSatpamController::class, 'dataSatpamOffByDate'])->name('dataSatpamOffByDate');
            Route::get('/check-pengganti/{id}', [\App\Http\Controllers\JadwalSatpamController::class, 'checkPengganti'])->name('checkPengganti');
        });

        Route::get('/datapresensi', [AdminController::class, 'datapresensi'])->name('admin.datapresensi');
        Route::get('/listkaryawan', [AdminController::class, 'listkaryawan'])->name('admin.listkaryawan');
        Route::get('/rekapitulasi', [AdminController::class, 'rekapitulasi'])->name('admin.rekapitulasi');
        Route::get('/rekapitulasikaryawan', [AdminController::class, 'rekapitulasikaryawan'])->name('admin.rekapitulasikaryawan');
        Route::get('/listrekapkaryawan', [AdminController::class, 'listrekapkaryawan'])->name('admin.listrekapkaryawan');
        Route::get('/detailrekap/{nip}', [AdminController::class, 'detailrekap'])->name('admin.detailrekap');
        Route::get('/listdetailrekapkaryawan/{nip}', [AdminController::class, 'listdetailrekapkaryawan'])->name('admin.listdetailrekapkaryawan');

        Route::get('createizinkehadiran/{id}', [AdminController::class, 'createizinkehadiran'])->name('admin.createizinkehadiran');
        Route::post('storeizinkehadiran', [AdminController::class, 'storeizinkehadiran'])->name('admin.storeizinkehadiran');
        Route::post('/biometric', [BiometricController::class, 'SyncAndInsertBiometric'])->name('admin.SyncAndInsertBiometric');
        Route::get('/biometricall', [BiometricAllController::class, 'SyncAndInsertBiometric'])->name('admin.SyncAndInsertBiometric');
        Route::get('printizin/{id}', [AdminController::class, 'printizin'])->name('admin.printizin');


        Route::get('/dataizin', [AdminController::class, 'dataizin'])->name('admin.dataizin');
        Route::get('/dataizin/{id}', [AdminController::class, 'dataizin_show'])->name('admin.dataizin.show');
        Route::get('/listizin', [AdminController::class, 'listizin'])->name('admin.listizin');
        Route::get('createizin', [AdminController::class, 'createizin'])->name('admin.createizin');
        Route::post('storeizin', [AdminController::class, 'storeizin'])->name('admin.storeizin');
        Route::get('/batal_izin/{id}', [AdminController::class, 'batal_izin'])->name('admin.batal_izin');
        Route::get('printizinkerja/{id}', [AdminController::class, 'printizinkerja'])->name('admin.printizinkerja');

        Route::get('/datacuti', [AdminController::class, 'datacuti'])->name('admin.datacuti');
        Route::get('/datacuti/{id}', [AdminController::class, 'datacuti_show'])->name('admin.datacuti.show');
        Route::post('/datacuti/pengganti', [AdminController::class, 'datacuti_pengganti'])->name('admin.datacuti.pengganti');
        Route::get('/datacuti/calendar/{id}/{nopeg}', [AdminController::class, 'datacuti_calendar'])->name('admin.datacuti.calendar');
        Route::get('/listcuti', [AdminController::class, 'listcuti'])->name('admin.listcuti');
        Route::get('createcuti', [AdminController::class, 'createcuti'])->name('admin.createcuti');
        Route::post('storecuti', [AdminController::class, 'storecuti'])->name('admin.storecuti');
        Route::get('/batal_cuti/{id}', [AdminController::class, 'batal_cuti'])->name('admin.batal_cuti');
        Route::get('printcuti/{id}', [AdminController::class, 'printcuti'])->name('admin.printcuti');

        Route::get('/liburnasional', [AdminController::class, 'liburnasional'])->name('admin.liburnasional');
        Route::get('/listlibur', [AdminController::class, 'listlibur'])->name('admin.listlibur');
        Route::get('/editlibur/{id}', [AdminController::class, 'editlibur'])->name('admin.editlibur');
        Route::post('/updatelibur', [AdminController::class, 'updatelibur'])->name('admin.updatelibur');
        Route::post('/createlibur', [AdminController::class, 'createlibur'])->name('admin.createlibur');
        Route::get('/destroylibur/{id}', [AdminController::class, 'destroylibur'])->name('admin.destroylibur');


        Route::get('/kuesioner', [KuesionerController::class, 'kuesioner'])->name('admin.kuesioner');
        Route::get('/storekuesioner', [KuesionerController::class, 'storekuesioner'])->name('admin.storekuesioner');
    });
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::get('/datapresensi', [KaryawanController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/datarekapitulasi', [KaryawanController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listdatapresensi', [KaryawanController::class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [KaryawanController::class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/listdatarekapitulasi', [KaryawanController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');
        Route::get('/izin/index', [KaryawanController::class, 'index_izin'])->name('izin');
        Route::post('/izin/store', [KaryawanController::class, 'store_izin'])->name('store_izin');
        Route::get('/izin/batal{id}', [KaryawanController::class, 'batal_izin'])->name('batal_izin');
        Route::get('/cuti/index', [KaryawanController::class, 'index_cuti'])->name('cuti');
        Route::post('/cuti/store', [KaryawanController::class, 'store_cuti'])->name('store_cuti');
        Route::get('/cuti/batal/{id}', [KaryawanController::class, 'batal_cuti'])->name('batal_cuti');

        Route::get('createizinkehadiran/{id}', [KaryawanController::class, 'createizinkehadiran'])->name('createizinkehadiran');
        Route::post('storeizinkehadiran', [KaryawanController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
        Route::get('printizin/{id}', [KaryawanController::class, 'printizin'])->name('printizin');
    });

    Route::group(['prefix' => 'kepalaunit'], function () {

        Route::get('/kepalaunit', [KepalaUnitController::class, 'kepalaunit'])->name('kepalaunit.kepalaunit');
        Route::get('/dataizin', [KepalaUnitController::class, 'dataizin'])->name('kepalaunit.dataizin');
        Route::get('/editizin/{id_izinkerja}', [KepalaUnitController::class, 'editizin'])->name('kepalaunit.editizin');
        Route::post('/updateizin', [KepalaUnitController::class, 'updateizin'])->name('kepalaunit.updateizin');

        Route::get('/datacuti', [KepalaUnitController::class, 'datacuti'])->name('kepalaunit.datacuti');
        Route::get('/editcuti/{id_cuti}', [KepalaUnitController::class, 'editcuti'])->name('kepalaunit.editcuti');
        Route::post('/updatecuti', [KepalaUnitController::class, 'updatecuti'])->name('kepalaunit.updatecuti');
        Route::get('/batal_cuti/{id}', [KepalaUnitController::class, 'batal_cuti'])->name('kepalaunit.batal_cuti');
    });
});
