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

        Route::get('createizin', [AdminController::Class, 'createizin'])->name('admin.createizin');
        Route::post('storeizin', [AdminController::Class, 'storeizin'])->name('admin.storeizin');

        Route::get('createcuti', [AdminController::Class, 'createcuti'])->name('admin.createcuti');
        Route::post('storecuti', [AdminController::Class, 'storecuti'])->name('admin.storecuti');
    });
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::Class, 'index'])->name('index');
        Route::get('/datapresensi', [KaryawanController::Class, 'index_datapresensi'])->name('datapresensi');
        Route::get('/listdatapresensi', [KaryawanController::Class, 'listdatapresensi'])->name('listdatapresensi');
        Route::get('/rekapitulasi', [KaryawanController::Class, 'rekapitulasi'])->name('rekapitulasi');
        Route::get('/izin/index', [KaryawanController::Class, 'izin_index'])->name('izin_index');
        Route::get('/cuti/index', [KaryawanController::Class, 'cuti_index'])->name('cuti_index');
    });
});
