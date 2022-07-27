<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

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
    Route::get('/', [AuthController::Class,'index'])->name('auth.login_v');
    Route::post('login', [AuthController::Class,'login'])->name('auth.login');
    Route::get('logout', [AuthController::Class,'logout'])->name('auth.logout');
});

Route::group(['middleware' => 'auth'], function () {

    Route::group(['name' => 'admin'], function () {
        Route::get('/admin',[AdminController::Class,'index'])->name('admin.admin_v');
        Route::get('/datapresensi',[AdminController::Class,'datapresensi'])->name('admin.datapresensi');
        Route::get('/listkaryawan',[AdminController::Class,'listkaryawan'])->name('admin.listkaryawan');
        Route::get('/rekapitulasi',[AdminController::Class,'rekapitulasi'])->name('admin.rekapitulasi');
        Route::get('/rekapitulasikaryawan',[AdminController::Class,'rekapitulasikaryawan'])->name('admin.rekapitulasikaryawan');
        Route::get('/listrekapkaryawan',[AdminController::Class,'listrekapkaryawan'])->name('admin.listrekapkaryawan');
        
    });

   
});
