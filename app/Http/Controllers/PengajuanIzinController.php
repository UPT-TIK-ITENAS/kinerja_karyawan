<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Izin;
use App\Models\QR;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PengajuanIzinController extends Controller
{
    public function pengajuan_izin (){

        $title = "Karyawan";
        $izin = JenisIzin::get();

        $data_izin = Izin::join('users', 'users.nopeg', '=', 'izin.nopeg')->where('izin.nopeg', auth()->user()->nopeg)->get();
        // dd($data_izin);
        $pageConfigs = ['layoutWidth' => 'full'];
        $breadcrumbs = [
            ['name' => "Home", 'link' => 'home'],
            ['name' => "Pengajuan Izin"]
        ];

        return view ('content.izin.izin', compact('title','izin','data_izin','pageConfigs', 'breadcrumbs'));
    }

    public function tambah_izin (){

        $title = "Karyawan";
        $izin = JenisIzin::get();
        $pageConfigs = ['layoutWidth' => 'full'];
        $breadcrumbs = [
            ['name' => "Home", 'link' => 'home'],
            ['name' => "Pengajuan Izin", 'link' => 'cuti'],
            ['name' => "Tambah Pengajuan Izin"]
        ];
        return view ('content.izin.tambah_izin', compact('title','izin','pageConfigs', 'breadcrumbs',));
    }

    function save_izin(Request $request)
    {

        Izin::insert([
            'nopeg' => $request->nopeg,
            'name' => $request->name,
            'unit' => $request->unit,
            'tgl_awal_izin' => $request->tgl_awal_izin,
            'tgl_akhir_izin' => $request->tgl_akhir_izin,
            'jam_awal_izin' => $request->jam_awal_izin,
            'jam_akhir_izin' => $request->jam_akhir_izin,
            'alasan' => $request->alasan,
            'validasi' => $request->validasi,
        ]);

        $dataqr = Izin::where('nopeg', auth()->user()->nopeg)->first();
        $qrcode_filename = 'qr-' . base64_encode(auth()->user()->nopeg . date('Y-m-d H:i:s')) . '.svg';
        // dd($qrcode_filename);
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . ' Pada tanggal ' . $dataqr->validate_at, public_path("qrcode/" . $qrcode_filename));

        QR::where('nrp', auth()->user()->nopeg)->insert([
            'nopeg' => auth()->user()->nopeg,
            'qr_peg' => $qrcode_filename,
        ]);


        return redirect()->route('content.izin.pengajuan_izin')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizin()
    {

        $pdf = PDF::loadview('content.izin.printizin')->setPaper('A4', 'potrait');
        return $pdf->stream();
    }
}
