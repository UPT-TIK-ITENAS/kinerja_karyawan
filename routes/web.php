<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PengajuanCutiController;

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
    Route::get('/', [AuthController::Class, 'index'])->name('auth.login_v');
    Route::post('login', [AuthController::Class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::Class, 'logout'])->name('auth.logout');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin', [AdminController::Class, 'index'])->name('admin.admin_v');
    Route::group(['prefix' => 'admin'], function () {

        Route::get('/datapresensi', [AdminController::Class, 'datapresensi'])->name('admin.datapresensi');
        Route::get('/listkaryawan', [AdminController::Class, 'listkaryawan'])->name('admin.listkaryawan');
        Route::get('/rekapitulasi', [AdminController::Class, 'rekapitulasi'])->name('admin.rekapitulasi');
        Route::get('/rekapitulasikaryawan', [AdminController::Class, 'rekapitulasikaryawan'])->name('admin.rekapitulasikaryawan');
        Route::get('/listrekapkaryawan', [AdminController::Class, 'listrekapkaryawan'])->name('admin.listrekapkaryawan');

        Route::get('createizinkehadiran/{id}', [AdminController::Class, 'createizinkehadiran'])->name('admin.createizinkehadiran');
        Route::post('storeizinkehadiran', [AdminController::Class, 'storeizinkehadiran'])->name('admin.storeizinkehadiran');
        Route::get('/biometric', [BiometricController::Class, 'SyncAndInsertBiometric'])->name('admin.SyncAndInsertBiometric');
        Route::get('printizin/{id}', [AdminController::Class, 'printizin'])->name('admin.printizin');

        Route::get('/dataizin', [AdminController::Class, 'dataizin'])->name('admin.dataizin');
        Route::get('/listizin', [AdminController::Class, 'listizin'])->name('admin.listizin');
        Route::get('createizin', [AdminController::Class, 'createizin'])->name('admin.createizin');
        Route::post('storeizin', [AdminController::Class, 'storeizin'])->name('admin.storeizin');
        Route::get('printizinkerja/{id}', [AdminController::Class, 'printizinkerja'])->name('admin.printizinkerja');

        Route::get('/datacuti', [AdminController::Class, 'datacuti'])->name('admin.datacuti');
        Route::get('/listcuti', [AdminController::Class, 'listcuti'])->name('admin.listcuti');
        Route::get('createcuti', [AdminController::Class, 'createcuti'])->name('admin.createcuti');
        Route::post('storecuti', [AdminController::Class, 'storecuti'])->name('admin.storecuti');
        Route::get('printcuti/{id}', [AdminController::Class, 'printcuti'])->name('admin.printcuti');


        Route::get('/datapresensi', [AdminController::Class, 'datapresensi'])->name('admin.datapresensi');
        Route::get('/listkaryawan', [AdminController::Class, 'listkaryawan'])->name('admin.listkaryawan');
        Route::get('/rekapitulasi', [AdminController::Class, 'rekapitulasi'])->name('admin.rekapitulasi');
        Route::get('/rekapitulasikaryawan', [AdminController::Class, 'rekapitulasikaryawan'])->name('admin.rekapitulasikaryawan');
        Route::get('/listrekapkaryawan', [AdminController::Class, 'listrekapkaryawan'])->name('admin.listrekapkaryawan');

        Route::get('createizinkehadiran/{id}', [AdminController::Class, 'createizinkehadiran'])->name('admin.createizinkehadiran');
        Route::post('storeizinkehadiran', [AdminController::Class, 'storeizinkehadiran'])->name('admin.storeizinkehadiran');
        Route::get('/biometric', [BiometricController::Class, 'SyncAndInsertBiometric'])->name('admin.SyncAndInsertBiometric');
        Route::get('printizin/{id}', [AdminController::Class, 'printizin'])->name('admin.printizin');

        Route::get('createizin', [AdminController::Class, 'createizin'])->name('admin.createizin');
        Route::post('storeizin', [AdminController::Class, 'storeizin'])->name('admin.storeizin');

        Route::get('createcuti', [AdminController::Class, 'createcuti'])->name('admin.createcuti');
        Route::post('storecuti', [AdminController::Class, 'storecuti'])->name('admin.storecuti');
    });
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::Class, 'index'])->name('index');
        Route::get('/datapresensi', [KaryawanController::Class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/datarekapitulasi', [KaryawanController::Class, 'index_datarekapitulasi'])->name('datarekapitulasi');
        Route::get('/listdatapresensi', [KaryawanController::Class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [KaryawanController::Class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/listdatarekapitulasi', [KaryawanController::Class, 'listdatarekapitulasi'])->name('listdatarekapitulasi');
        Route::get('/izin/index', [KaryawanController::Class, 'index_izin'])->name('izin');
        Route::post('/izin/store', [KaryawanController::Class, 'store_izin'])->name('store_izin');
        Route::get('/izin/batal{id}', [KaryawanController::Class, 'batal_izin'])->name('batal_izin');
        Route::get('/cuti/index', [KaryawanController::Class, 'index_cuti'])->name('cuti');
        Route::post('/cuti/store', [KaryawanController::Class, 'store_cuti'])->name('store_cuti');
        Route::get('/cuti/batal/{id}', [KaryawanController::Class, 'batal_cuti'])->name('batal_cuti');
    });
});
