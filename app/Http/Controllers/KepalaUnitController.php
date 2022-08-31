<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KepalaUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('kepalaunit.kepalaunit_v');
    }

    public function dataizin()
    {
        $data = IzinKerja::select('izin_kerja.*', 'jenis_izin.jenis_izin as nama_izin')
        ->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')
        ->join('users','izin_kerja.nopeg','=','users.nopeg')
        ->where('users.atasan',auth()->user()->nopeg)->get();
        return view('kepalaunit.ku_dataizin', compact('data'));
    }

    public function editizin($id_izinkerja)
    {
        $z = IzinKerja::selectRaw('izin_kerja.*,jenis_izin.jenis_izin as namaizin')->join('jenis_izin','jenis_izin.id_izin','=','izin_kerja.jenis_izin')->where('id_izinkerja',$id_izinkerja)->first();   
        return response()->json($z);
    }
    public function updateizin(Request $request)
    {
        $request->validate([
            'approval' => 'required',
        ]);
        $data = [
            'approval' => $request->approval,
        ];

        IzinKerja::where('id_izinkerja', $request->id_izinkerja)->update($data);
        return redirect()->route('kepalaunit.dataizin')->with('success', 'Approval berhasil!');
    }

    
   

    public function datacuti()
    {
        $data = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
        ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
        ->join('users','cuti.nopeg','=','users.nopeg')
        ->where('users.atasan',auth()->user()->nopeg)->get();
        
        return view('kepalaunit.ku_datacuti', compact('data'));
    }

    public function editcuti($id_cuti)
    {
        $c = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')->where('cuti.id_cuti',$id_cuti)->first(); 
        return response()->json($c);
    }

    public function updatecuti(Request $request)
    {
        $request->validate([
            'approval' => 'required',
        ]);
        $data = [
            'approval' => $request->approval,
        ];

        Cuti::where('id_cuti', $request->id_cuti)->update($data);
        return redirect()->route('kepalaunit.datacuti')->with('success', 'Approval berhasil!');
    }

}
