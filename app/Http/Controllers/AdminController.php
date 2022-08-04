<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Attendance;
use App\Models\izin;
use App\Models\cuti;
use App\Models\JenisCuti;
use App\Models\QR;
use PDF;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin_v');
    }

    public function datapresensi()
    {
        return view('admin.datapresensi');
    }

    
    public function listkaryawan(Request $request)
    {
        $data =Attendance::selectRaw('attendance.*, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('duration', function ($row) {
                    if ($row->jam_pulang == NULL || $row->jam_masuk == NULL) {
                        return $durationwork = '';
                    } else {
                        $time_awalreal =  strtotime($row->jam_masuk);
                        $time_akhirreal = strtotime($row->jam_pulang);
                        $duration = ceil(abs($time_akhirreal - $time_awalreal) - strtotime('01:00:00'));
                        $durationwork = date("H:i:s", $duration);
                        return $durationwork;
                    }
                })
                ->editColumn('hari', function ($row) {
                    return config('app.days')[$row->hari];
                })

                ->addColumn('latemasuk', function ($row) {

                    if (date("H:i:s", strtotime($row->jam_masuk)) <= '08:00:00') {
                        return '';
                    } else if (date("H:i:s", strtotime($row->jam_masuk)) > '08:00:00' && $row->hari != '6') {
                        $durasitelat = strtotime($row->jam_masuk) - strtotime('08:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    }
                })
                ->addColumn('latesiang', function ($row) {
                    if ($row->hari == 5) {
                        if (date("H:i:s", strtotime($row->jam_siang)) <= '13:15:00') {
                            return '';
                        } else if (date("H:i:s", strtotime($row->jam_siang)) > '13:30:00') {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        }
                    } else {
                        if ($row->hari != 6 && date("H:i:s", strtotime($row->jam_siang)) <= '12:45:00') {
                            return '';
                        } else if (date("H:i:s", strtotime($row->jam_siang)) > '13:00:00') {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        }
                    }
                })

                ->addColumn('action', function ($row) {

                    $dataizin = Attendance::join('izin','izin.id_attendance','=','attendance.id')->first();
                    $addsurat = route('admin.createizinkehadiran', $row->id);
                    $printsurat =  route('admin.printizin', $dataizin->id);

                    if($row->id != $dataizin->id_attendance){
                        $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='$addsurat' class='btn btn btn-success align-items-center'><i class='icofont icofont-ui-add'></i></a>
                        </div>
                        ";
                        return $actionBtn;
                    }else{
                        $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='$addsurat' class='btn btn btn-success align-items-center'><i class='icofont icofont-ui-add'></i></a>
                            <a href='$printsurat' class='btn btn btn-success align-items-center'><i class='icofont icofont-download-alt'></i></a>
                        </div>
                        ";
                        return $actionBtn;
                    }
                })
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang','action'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }


    public function rekapitulasi()
    {
        return view('admin.rekapitulasi');
    }

    public function listrekapkaryawan(Request $request)
    {
        $data = DB::table('users');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('duration', function ($datauser) {

                    $durasi_telat = strtotime('00:00:00');
                    $data_att     = Attendance::where('nip', $datauser->nopeg)->get();
                    foreach ($data_att as $row) {
                        if (date("H:i:s", strtotime($row->jam_masuk)) > $datauser->awal_tugas && $row->hari != '6') {
                            $durasitelat = strtotime($row->jam_masuk) - strtotime($datauser->awal_tugas);
                            $durasi_telat += $durasitelat;
                        }
                        if ($row->hari == '5') {
                            if (date("H:i:s", strtotime($row->jam_siang)) > '13:30:00') {
                                $durasitelat = strtotime($row->jam_siang) - strtotime('13:30:00');
                                $durasi_telat += $durasitelat;
                            }
                        } else {
                            if (date("H:i:s", strtotime($row->jam_siang)) > '13:00:00') {
                                $durasitelat = strtotime($row->jam_siang) - strtotime('13:00:00');
                                $durasi_telat += $durasitelat;
                            }
                        }
                    }
                    return date("H:i:s", $durasi_telat);
                })
                ->rawColumns(['duration'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function createizinkehadiran($id)
    {

        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg')->where('attendance.id',$id)->first();
        return view('admin.createizinkehadiran',compact('data'));
    }

    public function storeizinkehadiran(Request $request)
    {
        Izin::insert([
            'id_attendance' => $request->id_attendance,
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

        $dataqr = Izin::where('nopeg', $request->nopeg)->first();
        $qrcode_filename = 'qr-' . base64_encode($request->nopeg . date('Y-m-d H:i:s')) . '.svg';
        // dd($qrcode_filename);
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->nopeg .'-'. $request->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

        QR::where('nopeg', $request->nopeg)->insert([
            'id_attendance' => $request->id_attendance,
            'nopeg' => $request->nopeg,
            'qr_peg' => $qrcode_filename,
        ]);

        return redirect()->route('admin.datapresensi')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();
        $dataqr = QR::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.printizin', compact('data','dataqr'))->setPaper('potrait');
        return $pdf->stream();
    }
    
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

    public function createcuti()
    {
        $datauser = User::groupby('nopeg')->get();
        $jeniscuti = JenisCuti::get();
        // $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg')->get();
        return view('admin.createcuti',compact('datauser','jeniscuti'));
    }

    public function storecuti(Request $request)
    {

        // dd($request->startDate);
        // $tanggal = explode('-',$request->tanggal);
        // $tanggal_awal_cuti = date('d-m-Y', strtotime($tanggal[0]));
        // $tanggal_akhir_cuti = date('d-m-Y', strtotime($tanggal[1]));

        // Cuti::insert([
        //     'nopeg' => $request->nopeg,
        //     'name' => $request->name,
        //     'unit' => $request->unit,
        //     'tgl_awal_cuti' => $tanggal_awal_cuti,
        //     'tgl_akhir_cuti' => $tanggal_akhir_cuti,
        //     'alasan' => $request->alasan,
        //     'validasi' => $request->validasi,
        // ]);

        return redirect()->route('admin.createcuti')->with('success', 'Pengajuan cuti Berhasil!');
    }
   

    

    
}
