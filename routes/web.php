<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\BsdmController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\KepalaUnitController;
use App\Http\Controllers\BiometricAllController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\ListKaryawanController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\PejabatController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
    //    $jam_masuk = '08:00:00';
    //    $jam_siang = '13:37:00';
    //    $jam_pulang = '17:30:00';
    //    $telat_masuk = lateMasuk($jam_masuk, $jam_siang, 5);
    //    $telat_siang = lateSiang2($jam_siang, $jam_pulang, 5);
    //    dd($jam_masuk, $jam_siang, $jam_pulang, $telat_masuk, $telat_siang);


    //    $users = DB::table('users')->get();
    //    foreach ($users as $key => $value) {
    //        // transform password from tanggal lahir
    //        $password = Carbon::parse($value->tanggal_lahir)->format('dmY');
    //        $users[$key]->password = Hash::make($password);
    //    }
    //    dd($users);
    // dispatch(new \App\Jobs\MigrateAttendance('attendance_baru', 'attendance', '2022-08-31'));
    // dispatch(new \App\Jobs\GetAttendanceByDateFromBiometric('2023-01-10', 'attendance_baru'));
    // dd("Selesai!");

    // Send telegram notification
    // $user = User::where('nopeg', '1815')->first();
    // $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
    // dd("Selesai!");
});

Route::prefix('data')->name('data.')->group(function () {
    Route::prefix('unit')->name('unit.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Data\UnitController::class, 'index'])->name('index');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/atasan', [\App\Http\Controllers\Data\UserController::class, 'atasan'])->name('atasan');
        Route::get('/atasan-langsung', [\App\Http\Controllers\Data\UserController::class, 'atasan_langsung'])->name('atasan_langsung');
    });
});

Route::group(['middleware' => 'auth'], function () {
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

        Route::prefix('print')->name('admin.print.')->group(function () {
            Route::get('izin/{id}', [PrintController::class, 'printizin'])->name('izin');
            Route::get('izinkerja/{id}', [PrintController::class, 'printizinkerja'])->name('izinkerja');
            Route::get('cuti/{id}', [PrintController::class, 'printcuti'])->name('cuti');
        });

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
            Route::get('/admlistPenilaian', [KuesionerController::class, 'admlistPenilaian'])->name('admlistPenilaian');
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
            Route::get('/list', [ListKaryawanController::class, 'list'])->name('list');
            Route::get('/', [ListKaryawanController::class, 'index'])->name('index');
            Route::post('/', [ListKaryawanController::class, 'store'])->name('store');
            Route::get('/{id}', [ListKaryawanController::class, 'show'])->name('show');
            Route::post('/{id}', [ListKaryawanController::class, 'update'])->name('update');
        });

        Route::prefix('rekapitulasi')->name('admin.rekapitulasi.')->group(function () {
            Route::get('/', [RekapitulasiController::class, 'index'])->name('rekap');
            Route::get('/listrekapkaryawan', [RekapitulasiController::class, 'listrekapkaryawan'])->name('listrekapkaryawan');
            Route::get('/detailrekap/{nopeg}', [RekapitulasiController::class, 'detailrekap'])->name('detailrekap');
            Route::get('/detailrekap/list/{nopeg}', [RekapitulasiController::class, 'list_detail_rekap'])->name('listdetailrekap');
            Route::get('/list-penilaian-detail/{tipe}', [RekapitulasiController::class, 'penilaian_detail'])->name('penilaian_detail');
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

        Route::prefix('izin-perhari')->name('izin-perhari.')->group(function () {
            Route::get('/', [AdminController::class, 'index_izin_perhari'])->name('index');
            Route::get('/edit/{id}', [AdminController::class, 'edit_izin_perhari'])->name('edit');
            Route::post('/update/{id}', [AdminController::class, 'update_izin_perhari'])->name('update');
        });

        Route::prefix('ajuan')->name('ajuan.')->group(function () {
            Route::get('', [AdminController::class, 'index_ajuan'])->name('index');
            Route::get('/{id}', [AdminController::class, 'detail_mangkir'])->name('detail');
            Route::post('/update', [AdminController::class, 'update_ajuan'])->name('update');
        });

        Route::prefix('karyawan')->name('karyawan.')->group(function () {
            Route::get('/list', [ListKaryawanController::class, 'list'])->name('list');
            Route::get('/', [ListKaryawanController::class, 'index'])->name('index');
            Route::post('/', [ListKaryawanController::class, 'store'])->name('store');
            Route::get('/{id}', [ListKaryawanController::class, 'show'])->name('show');
            Route::post('/{id}', [ListKaryawanController::class, 'update'])->name('update');
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
            Route::get('/', [BsdmController::class, 'bsdm_datapresensi'])->name('master');
            Route::get('/listkaryawan', [BsdmController::class, 'bsdm_listkaryawan'])->name('bsdm_listkaryawan');
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

        Route::prefix('kuesioner')->name('kuesioner.')->group(function () {
            Route::get('/admHasilKuesioner', [KuesionerController::class, 'admHasilKuesioner'])->name('admHasilKuesioner');
            Route::get('/admlistPenilaian', [KuesionerController::class, 'admlistPenilaian'])->name('admlistPenilaian');
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
    });


    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::prefix('jadwal-satpam')->name('jadwal-satpam.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'index'])->name('index');
            Route::get('/calendar-by-user/{id}', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'showDataCalendarByUser'])->name('calendar.by-user');
            Route::get('/by-id/{id}', [\App\Http\Controllers\Karyawan\JadwalSatpamController::class, 'showDataById'])->name('showDataById');
        });

        Route::prefix('print')->name('print.')->group(function () {
            Route::get('izin/{id}', [PrintController::class, 'printizin'])->name('izin');
            Route::get('izinkerja/{id}', [PrintController::class, 'printizinkerja'])->name('izinkerja');
            Route::get('cuti/{id}', [PrintController::class, 'printcuti'])->name('cuti');
        });

        Route::get('/datapresensi', [KaryawanController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/datarekapitulasi', [KaryawanController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listdatapresensi', [KaryawanController::class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [KaryawanController::class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/getWorkingDays/{startDate}/{endDate}', [KaryawanController::class, 'getWorkingDays'])->name('getWorkingDays');
        Route::get('/historycuti/{nopeg}/{jenis}', [KaryawanController::class, 'historycuti'])->name('historycuti');

        Route::get('/ajuan', [KaryawanController::class, 'ajuan_mangkir'])->name('ajuan');
        Route::post('/store_ajuan', [KaryawanController::class, 'store_ajuan'])->name('store_ajuan');


        Route::get('/calendar-by-user/{id}', [\App\Http\Controllers\KaryawanController::class, 'showDataCalendarByUser'])->name('calendar.by-user');
        Route::get('/show-data-calendar', [\App\Http\Controllers\KaryawanController::class, 'showDataCalendar'])->name('showDataById');
        // Route::get('/calendar', [\App\Http\Controllers\KaryawanController::class, 'allDataCalendar'])->name('calendar.all');

        // Route::get('/listdatarekapitulasi', [KaryawanController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');
        Route::get('/listdatarekapitulasi', [KaryawanController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');
        Route::get('/list-penilaian-detail/{tipe}', [KaryawanController::class, 'penilaian_detail'])->name('penilaian_detail');
        Route::get('/izin/index', [KaryawanController::class, 'index_izin'])->name('izin');
        Route::post('/izin/store', [KaryawanController::class, 'store_izin'])->name('store_izin');
        Route::get('/izin/batal{id}', [KaryawanController::class, 'batal_izin'])->name('batal_izin');
        Route::get('/cuti/index', [KaryawanController::class, 'index_cuti'])->name('cuti');
        Route::post('/cuti/store', [KaryawanController::class, 'store_cuti'])->name('store_cuti');
        Route::get('/cuti/batal/{id}', [KaryawanController::class, 'batal_cuti'])->name('batal_cuti');
        Route::get('/editAtt/{id}', [KaryawanController::class, 'editAtt'])->name('editAtt');
        Route::get('createizinkehadiran/{id}', [KaryawanController::class, 'createizinkehadiran'])->name('createizinkehadiran');
        Route::post('storeizinkehadiran', [KaryawanController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
        Route::get('printizin/{id}', [KaryawanController::class, 'printizin'])->name('printizin');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('editprofile', [KaryawanController::class, 'editprofile'])->name('editprofile');
            Route::post('update_profile', [KaryawanController::class, 'update_profile'])->name('update_profile');
        });
    });


    Route::prefix('pejabat')->name('pejabat.')->group(function () {
        Route::get('/', [PejabatController::class, 'index'])->name('index');

        Route::get('/datapresensi', [PejabatController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/listdatapresensi', [PejabatController::class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/datarekapitulasi', [PejabatController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listrekapkaryawan', [PejabatController::class, 'listrekapkaryawan'])->name('listrekapkaryawan');
        Route::get('/detailrekap/{nopeg}', [RekapitulasiController::class, 'detailrekap'])->name('detailrekap');

        Route::get('/rekapitulasi', [PejabatController::class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/listdatarekapitulasi', [PejabatController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');

        Route::prefix('print')->name('print.')->group(function () {
            Route::get('cuti/{id}', [PrintController::class, 'printcuti'])->name('cuti');
        });

        Route::get('/approval/index', [PejabatController::class, 'index_approval'])->name('approval');
        Route::get('/approval/editCuti/{id}', [PejabatController::class, 'editCuti'])->name('editCuti');
        Route::get('/approval/destroyCuti/{id}', [PejabatController::class, 'batal_cuti'])->name('destroyCuti');
        Route::post('/approval/approveCuti', [PejabatController::class, 'approveCuti'])->name('approveCuti');

        Route::get('createizinkehadiran/{id}', [PejabatController::class, 'createizinkehadiran'])->name('createizinkehadiran');
        Route::post('storeizinkehadiran', [PejabatController::class, 'storeizinkehadiran'])->name('storeizinkehadiran');
        Route::get('printizin/{id}', [PejabatController::class, 'printizin'])->name('printizin');
    });

    Route::prefix('kepalaunit')->name('kepalaunit.')->group(function () {
        //Dashboard
        Route::get('/', [KepalaUnitController::class, 'index'])->name('kepalaunit');

        //Data Presensi
        Route::get('/datapresensi', [KepalaUnitController::class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/listdatapresensi', [KepalaUnitController::class, 'listdatapresensi'])->name('listdatapresensi');

        //Rekapitulasi
        Route::get('/datarekapitulasi', [KepalaUnitController::class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listrekapkaryawan', [KepalaUnitController::class, 'listrekapkaryawan'])->name('listrekapkaryawan');
        Route::get('/detailrekap/{nopeg}', [KepalaUnitController::class, 'detailrekap'])->name('detailrekap');
        Route::get('/detailrekap/list/{nopeg}', [KepalaUnitController::class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');
        Route::get('/list-penilaian-detail/{tipe}', [KepalaUnitController::class, 'penilaian_detail'])->name('penilaian_detail');

        Route::get('/rekapitulasi', [KepalaUnitController::class, 'rekapitulasi'])->name('rekapitulasi');

        Route::prefix('print')->name('print.')->group(function () {
            Route::get('izin/{id}', [PrintController::class, 'printizin'])->name('izin');
            Route::get('izinkerja/{id}', [PrintController::class, 'printizinkerja'])->name('izinkerja');
            Route::get('cuti/{id}', [PrintController::class, 'printcuti'])->name('cuti');
        });

        Route::get('/ajuan', [KepalaUnitController::class, 'ajuan_mangkir'])->name('ajuan');
        Route::get('/ajuan/{id}', [KepalaUnitController::class, 'detail_mangkir'])->name('ajuan_detail');
        Route::post('/update_ajuan', [KepalaUnitController::class, 'update_ajuan'])->name('update_ajuan');

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
        Route::get('/kuesioner/listPenilaian', [KuesionerController::class, 'listPenilaian'])->name('listPenilaian');

        
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('editprofile', [KepalaUnitController::class, 'editprofile'])->name('editprofile');
            Route::post('update_profile', [KepalaUnitController::class, 'update_profile'])->name('update_profile');
        });

        // Route::get('/dataizin', [KepalaUnitController::class, 'dataizin'])->name('kepalaunit.dataizin');
        // Route::get('/editizin/{id_izinkerja}', [KepalaUnitController::class, 'editizin'])->name('kepalaunit.editizin');
        // Route::post('/updateizin', [KepalaUnitController::class, 'updateizin'])->name('kepalaunit.updateizin');

        // Route::get('/datacuti', [KepalaUnitController::class, 'datacuti'])->name('kepalaunit.datacuti');
        // Route::get('/editcuti/{id_cuti}', [KepalaUnitController::class, 'editcuti'])->name('kepalaunit.editcuti');
        // Route::post('/updatecuti', [KepalaUnitController::class, 'updatecuti'])->name('kepalaunit.updatecuti');
        // Route::get('/batal_cuti/{id}', [KepalaUnitController::class, 'batal_cuti'])->name('kepalaunit.batal_cuti');
    });
});
