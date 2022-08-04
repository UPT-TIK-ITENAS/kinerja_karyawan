<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cuti;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use PDF;
class PengajuanCutiController extends Controller
{
    public function createcuti()
    {
        $datauser = User::groupby('nopeg')->get();
        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg')->get();
        return view('admin.createizin',compact('data','datauser'));
    }

    public function storecuti(Request $request)
    {
        $tanggal = explode('-',$request->tanggal);
        $tanggal_awal_izin = date('d-m-Y', strtotime($tanggal[0]));
        $tanggal_akhir_izin = date('d-m-Y', strtotime($tanggal[1]));

        Cuti::insert([
            'nopeg' => $request->nopeg,
            'name' => $request->name,
            'unit' => $request->unit,
            'tgl_awal_izin' => $tanggal_awal_izin,
            'tgl_akhir_izin' => $tanggal_akhir_izin,
            'alasan' => $request->alasan,
            'validasi' => $request->validasi,
        ]);

        return redirect()->route('admin.createizin')->with('success', 'Pengajuan Izin Berhasil!');
    }
}
