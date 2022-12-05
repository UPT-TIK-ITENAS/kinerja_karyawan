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
use App\Http\Controllers\MesinController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\ListKaryawanController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\PejabatController;
use Illuminate\Support\Carbon;

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
Route::get('/test', function () {
    dd(lateMasuk(null, "2021-05-01 13:00:48", 3));
});
Route::group(['middleware' => 'auth'], function () {


    Route::get('printizin/{id}', [AdminController::class, 'printizin'])->name('admin.printizin');
    Route::get('printizinkerja/{id}', [AdminController::class, 'printizinkerja'])->name('admin.printizinkerja');
    Route::get('printcuti/{id}', [AdminController::class, 'printcuti'])->name('admin.printcuti');

    Route::group(['prefix' => 'admin'], function () {

        Route::get('/', [AdminController::class, 'index'])->name('admin.admin_v');
        Route::get('/kepalaunit', [KepalaUnitController::class, 'index'])->name('kepalaunit.kepalaunit_v');

        Route::post('/biometric', [BiometricController::class, 'SyncAndInsertBiometric'])->name('admin.SyncAndInsertBiometric');
        Route::post('/biometric-duration', [BiometricController::class, 'SyncAndInsertBiometricWithDuration'])->name('admin.SyncAndInsertBiometricWithDuration');
        Route::post('/biometric-recalculate-telat', [BiometricController::class, 'recalculateTelat'])->name('admin.recalculateTelat');
        Route::get('/biometricall', [BiometricAllController::class, 'SyncAndInsertBiometric'])->name('admin.biometricall');

        Route::get('/getWorkingDays/{startDate}/{endDate}', [AdminController::class, 'getWorkingDays'])->name('admin.getWorkingDays');
        Route::get('/historycuti/{nopeg}/{jenis}', [AdminController::class, 'historycuti'])->name('admin.historycuti');

        Route::get('/datacuti/{id}', [AdminController::class, 'datacuti_show'])->name('admin.datacuti.show');
        Route::post('/datacuti/pengganti', [AdminController::class, 'datacuti_pengganti'])->name('admin.datacuti.pengganti');
        Route::get('/datacuti/calendar/{id}/{nopeg}', [AdminController::class, 'datacuti_calendar'])->name('admin.datacuti.calendar');

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

        Route::prefix('presensi')->name('admin.presensi.')->group(function () {
            Route::get('/', [AdminController::class, 'datapresensi'])->name('master');
            Route::get('/with-duration', [AdminController::class, 'datapresensi_duration'])->name('master.duration');
            Route::get('/listkaryawan', [AdminController::class, 'listkaryawan'])->name('listkaryawan');
            Route::get('/listkaryawan-duration', [AdminController::class, 'listkaryawan_duration'])->name('listkaryawan-duration');
            Route::post('/storeizinkehadiran', [AdminController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
            Route::post('/storeAttendance', [AdminController::class, 'storeAttendance'])->name('storeAttendance');
            Route::post('/updateAttendance', [AdminController::class, 'updateAttendance'])->name('updateAttendance');
            Route::get('/editAtt/{id}', [AdminController::class, 'editAtt'])->name('editAtt');
        });

        Route::prefix('izin-resmi')->name('admin.izin-resmi.')->group(function () {
            Route::get('/', [AdminController::class, 'dataizin'])->name('dataizin');
            Route::get('/listizin', [AdminController::class, 'listizin'])->name('listizin');
            Route::post('storeizin', [AdminController::class, 'storeizin'])->name('storeizin');
            Route::get('/batal_izin/{id}', [AdminController::class, 'batal_izin'])->name('batal_izin');
        });

        Route::prefix('cuti')->name('admin.cuti.')->group(function () {
            Route::get('/', [AdminController::class, 'datacuti'])->name('datacuti');
            Route::get('/listcuti', [AdminController::class, 'listcuti'])->name('listcuti');
            Route::post('/storecuti', [AdminController::class, 'storecuti'])->name('storecuti');
            Route::get('/batal_cuti/{id}', [AdminController::class, 'batal_cuti'])->name('batal_cuti');
        });

        Route::prefix('libur-nasional')->name('admin.libur-nasional.')->group(function () {
            Route::get('/', [AdminController::class, 'liburnasional'])->name('libur');
            Route::get('/listlibur', [AdminController::class, 'listlibur'])->name('listlibur');
            Route::get('/editlibur/{id}', [AdminController::class, 'editlibur'])->name('editlibur');
            Route::post('/updatelibur', [AdminController::class, 'updatelibur'])->name('updatelibur');
            Route::post('/createlibur', [AdminController::class, 'createlibur'])->name('createlibur');
            Route::get('/destroylibur/{id}', [AdminController::class, 'destroylibur'])->name('destroylibur');
        });

        Route::prefix('mesin-sidikjari')->name('admin.mesin-sidikjari.')->group(function () {
            Route::get('/', [MesinController::class, 'index'])->name('mesin');
            Route::get('/editmesin/{id}', [MesinController::class, 'editmesin'])->name('editmesin');
            Route::post('/updatemesin', [MesinController::class, 'updatemesin'])->name('updatemesin');
            Route::post('/createmesin', [MesinController::class, 'createmesin'])->name('createmesin');
            Route::get('/destroymesin/{id}', [MesinController::class, 'destroymesin'])->name('destroymesin');
        });

        Route::prefix('kuesioner')->name('admin.kuesioner.')->group(function () {
            Route::get('/admHasilKuesioner', [KuesionerController::class, 'admHasilKuesioner'])->name('admHasilKuesioner');
            Route::get('/pertanyaanPeriode', [KuesionerController::class, 'pertanyaanPeriode'])->name('pertanyaanPeriode');
            Route::get('/editPeriode/{id}', [KuesionerController::class, 'editPeriode'])->name('editPeriode');
            Route::post('/updatePeriode', [KuesionerController::class, 'updatePeriode'])->name('updatePeriode');
            Route::post('/createPeriode', [KuesionerController::class, 'createPeriode'])->name('createPeriode');
            Route::get('/destroyPeriode/{id}', [KuesionerController::class, 'destroyPeriode'])->name('destroyPeriode');
            Route::get('/pertanyaan', [KuesionerController::class, 'pertanyaan'])->name('pertanyaan');
            Route::get('/editPertanyaan/{id}', [KuesionerController::class, 'editPertanyaan'])->name('editPertanyaan');
            Route::post('/updatePertanyaan', [KuesionerController::class, 'updatePertanyaan'])->name('updatePertanyaan');
            Route::get('/jawaban/{id}', [KuesionerController::class, 'jawaban'])->name('jawaban');
            Route::get('/editJawaban/{id}', [KuesionerController::class, 'editJawaban'])->name('editJawaban');
            Route::post('/updateJawaban', [KuesionerController::class, 'updateJawaban'])->name('updateJawaban');
        });

        Route::prefix('karyawan')->name('admin.karyawan.')->group(function () {
            Route::get('/', [ListKaryawanController::class, 'index'])->name('list');
        });

        Route::prefix('rekapitulasi')->name('admin.rekapitulasi.')->group(function () {
            Route::get('/', [RekapitulasiController::class, 'index'])->name('rekap');
            Route::get('/listrekapkaryawan', [RekapitulasiController::class, 'listrekapkaryawan'])->name('listrekapkaryawan');
            Route::get('/detailrekap/{nopeg}', [RekapitulasiController::class, 'detailrekap'])->name('detailrekap');
        });
    });

    Route::prefix('admin_bsdm')->name('admin_bsdm.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin_v');
        Route::prefix('izin-resmi')->name('izin-resmi.')->group(function () {
            Route::get('/', [AdminController::class, 'dataizin'])->name('dataizin');
            Route::get('/listizin', [AdminController::class, 'listizin'])->name('listizin');
            Route::post('storeizin', [AdminController::class, 'storeizin'])->name('storeizin');
            Route::get('/batal_izin/{id}', [AdminController::class, 'batal_izin'])->name('batal_izin');
            Route::get('printizinkerja/{id}', [AdminController::class, 'printizinkerja'])->name('printizinkerja');
        });

        Route::prefix('libur-nasional')->name('libur-nasional.')->group(function () {
            Route::get('/', [AdminController::class, 'liburnasional'])->name('libur');
            Route::get('/listlibur', [AdminController::class, 'listlibur'])->name('listlibur');
            Route::get('/editlibur/{id}', [AdminController::class, 'editlibur'])->name('editlibur');
            Route::post('/updatelibur', [AdminController::class, 'updatelibur'])->name('updatelibur');
            Route::post('/createlibur', [AdminController::class, 'createlibur'])->name('createlibur');
            Route::get('/destroylibur/{id}', [AdminController::class, 'destroylibur'])->name('destroylibur');
        });

        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [AdminController::class, 'datacuti'])->name('datacuti');
            Route::get('/listcuti', [AdminController::class, 'listcuti'])->name('listcuti');
            Route::post('/storecuti', [AdminController::class, 'storecuti'])->name('storecuti');
            Route::get('/batal_cuti/{id}', [AdminController::class, 'batal_cuti'])->name('batal_cuti');
            Route::get('printcuti/{id}', [AdminController::class, 'printcuti'])->name('printcuti');
        });

        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [AdminController::class, 'datapresensi'])->name('master');
            Route::get('/listkaryawan', [AdminController::class, 'listkaryawan'])->name('listkaryawan');
            Route::post('/storeAttendance', [AdminController::class, 'storeAttendance'])->name('storeAttendance');
            Route::post('/storeizinkehadiran', [AdminController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
            Route::post('/updateAttendance', [AdminController::class, 'updateAttendance'])->name('updateAttendance');
            Route::get('/editAtt/{id}', [AdminController::class, 'editAtt'])->name('editAtt');
            Route::get('printizin/{id}', [AdminController::class, 'printizin'])->name('printizin');
        });

        Route::prefix('rekapitulasi')->name('rekapitulasi.')->group(function () {
            Route::get('/', [RekapitulasiController::class, 'index'])->name('rekap');
            Route::get('/listrekapkaryawan', [RekapitulasiController::class, 'listrekapkaryawan'])->name('listrekapkaryawan');
            Route::get('/detailrekap/{nopeg}', [RekapitulasiController::class, 'detailrekap'])->name('detailrekap');
        });
    });


    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::prefix('jadwal-satpam')->name('jadwal-satpam.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'index'])->name('index');
            Route::get('/calendar-by-user/{id}', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'showDataCalendarByUser'])->name('calendar.by-user');
            Route::get('/by-id/{id}', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'showDataById'])->name('showDataById');
        });
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


    Route::prefix('pejabat')->name('pejabat.')->group(function () {
        Route::get('/', [PejabatController::class, 'index'])->name('index');

        Route::get('/datapresensi', [PejabatController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/datarekapitulasi', [PejabatController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listdatapresensi', [PejabatController::class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [PejabatController::class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/listdatarekapitulasi', [PejabatController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');

        Route::get('/izin/index', [PejabatController::class, 'index_izin'])->name('izin');
        Route::post('/izin/store', [PejabatController::class, 'store_izin'])->name('store_izin');
        Route::get('/izin/batal{id}', [PejabatController::class, 'batal_izin'])->name('batal_izin');

        Route::get('/cuti/index', [PejabatController::class, 'index_cuti'])->name('cuti');
        Route::post('/cuti/store', [PejabatController::class, 'store_cuti'])->name('store_cuti');
        Route::get('/cuti/batal/{id}', [PejabatController::class, 'batal_cuti'])->name('batal_cuti');

        Route::get('/approval/index', [PejabatController::class, 'index_approval'])->name('approval');
        Route::get('/approval/editCuti/{id}', [PejabatController::class, 'editCuti'])->name('editCuti');
        Route::get('/approval/destroyCuti/{id}', [PejabatController::class, 'batal_cuti'])->name('destroyCuti');
        Route::post('/approval/approveCuti', [PejabatController::class, 'approveCuti'])->name('approveCuti');

        Route::get('createizinkehadiran/{id}', [PejabatController::class, 'createizinkehadiran'])->name('createizinkehadiran');
        Route::post('storeizinkehadiran', [PejabatController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
        Route::get('printizin/{id}', [PejabatController::class, 'printizin'])->name('printizin');
    });





    Route::prefix('kepalaunit')->name('kepalaunit.')->group(function () {
        //Route::get('/', [KepalaUnitController::class, 'index'])->name('index');
        Route::get('/kepalaunit', [KepalaUnitController::class, 'index'])->name('kepalaunit');

        Route::get('/datapresensi', [KepalaUnitController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/datarekapitulasi', [KepalaUnitController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listdatapresensi', [KepalaUnitController::class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [KepalaUnitController::class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/listdatarekapitulasi', [KepalaUnitController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');

        Route::get('/izin/index', [KepalaUnitController::class, 'index_izin'])->name('izin');
        Route::post('/izin/store', [KepalaUnitController::class, 'store_izin'])->name('store_izin');
        Route::get('/izin/batal{id}', [KepalaUnitController::class, 'batal_izin'])->name('batal_izin');

        Route::get('/cuti/index', [KepalaUnitController::class, 'index_cuti'])->name('cuti');
        Route::post('/cuti/store', [KepalaUnitController::class, 'store_cuti'])->name('store_cuti');
        Route::get('/cuti/batal/{id}', [KepalaUnitController::class, 'batal_cuti'])->name('batal_cuti');

        Route::get('/approval/index', [KepalaUnitController::class, 'index_approval'])->name('approval');
        Route::get('/approval/editCuti/{id}', [KepalaUnitController::class, 'editCuti'])->name('editCuti');
        Route::get('/approval/destroyCuti/{id}', [KepalaUnitController::class, 'batal_cuti'])->name('destroyCuti');
        Route::post('/approval/approveCuti', [KepalaUnitController::class, 'approveCuti'])->name('approveCuti');

        Route::get('/approval/indexIzin', [KepalaUnitController::class, 'index_approvalIzin'])->name('approvalIzin');
        Route::get('/approval/editIzin/{id}', [KepalaUnitController::class, 'editIzin'])->name('editIzin');
        Route::get('/approval/destroyIzin/{id}', [KepalaUnitController::class, 'batal_izin'])->name('destroyIzin');
        Route::post('/approval/approveIzin', [KepalaUnitController::class, 'approveIzin'])->name('approveIzin');

        Route::get('/approval/indexIzinTelat', [KepalaUnitController::class, 'index_approvalIzinTelat'])->name('approvalIzinTelat');
        Route::get('/approval/editIzinTelat/{id}', [KepalaUnitController::class, 'editIzinTelat'])->name('editIzinTelat');
        Route::post('/approval/approveIzinTelat', [KepalaUnitController::class, 'approveIzinTelat'])->name('approveIzinTelat');

        Route::get('createizinkehadiran/{id}', [KepalaUnitController::class, 'createizinkehadiran'])->name('createizinkehadiran');
        Route::post('storeizinkehadiran', [KepalaUnitController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
        Route::get('printizin/{id}', [KepalaUnitController::class, 'printizin'])->name('printizin');

        Route::get('/pertanyaanPeriode', [KuesionerController::class, 'pertanyaanPeriode'])->name('pertanyaanPeriode');
        Route::get('/editPeriode/{id}', [KuesionerController::class, 'editPeriode'])->name('editPeriode');
        Route::post('/updatePeriode', [KuesionerController::class, 'updatePeriode'])->name('updatePeriode');
        Route::post('/createPeriode', [KuesionerController::class, 'createPeriode'])->name('createPeriode');
        Route::get('/destroyPeriode/{id}', [KuesionerController::class, 'destroyPeriode'])->name('destroyPeriode');

        Route::get('/kuesioner/index', [KuesionerController::class, 'indexKuesioner'])->name('indexKuesioner');
        Route::get('/kuesioner/kinerja/{id}', [KuesionerController::class, 'showKuesioner'])->name('showKuesioner');
        Route::post('/kuesioner/approveKuesioner/{id}', [KuesionerController::class, 'storeKuesioner'])->name('storeKuesioner');
        Route::get('/kuesioner/hasilKuesioner', [KuesionerController::class, 'index_penilaian'])->name('hasilKuesioner');

        // Route::get('/dataizin', [KepalaUnitController::class, 'dataizin'])->name('kepalaunit.dataizin');
        // Route::get('/editizin/{id_izinkerja}', [KepalaUnitController::class, 'editizin'])->name('kepalaunit.editizin');
        // Route::post('/updateizin', [KepalaUnitController::class, 'updateizin'])->name('kepalaunit.updateizin');

        // Route::get('/datacuti', [KepalaUnitController::class, 'datacuti'])->name('kepalaunit.datacuti');
        // Route::get('/editcuti/{id_cuti}', [KepalaUnitController::class, 'editcuti'])->name('kepalaunit.editcuti');
        // Route::post('/updatecuti', [KepalaUnitController::class, 'updatecuti'])->name('kepalaunit.updatecuti');
        // Route::get('/batal_cuti/{id}', [KepalaUnitController::class, 'batal_cuti'])->name('kepalaunit.batal_cuti');
    });
});
