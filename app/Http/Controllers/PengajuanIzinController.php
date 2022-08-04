<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Izin;
use App\Models\QR;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Attendance;
use App\Models\User;


class PengajuanIzinController extends Controller
{
    public function createizin()
    {
        $datauser = User::groupby('nopeg')->get();
        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg')->get();
        return view('admin.createizin',compact('data','datauser'));
    }

    public function storeizin(Request $request)
    {
        $tanggal = explode('-',$request->tanggal);
        $tanggal_awal_izin = date('d-m-Y', strtotime($tanggal[0]));
        $tanggal_akhir_izin = date('d-m-Y', strtotime($tanggal[1]));

        Izin::insert([
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
