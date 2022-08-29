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
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use Carbon\Carbon;
use App\Models\QR;
use PDF;

class AdminController extends Controller
{

    //DASHBOARD
    public function index()
    {
        return view('admin.admin_v');
    }

    //END DASHBOARD

    //DATA PRESENSI
    public function datapresensi()
    {
        return view('admin.datapresensi');
    }


    public function listkaryawan(Request $request)
    {
        $data = Attendance::selectRaw('attendance.*, users.name, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg');
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
                    $addsurat = route('admin.createizinkehadiran', $row->id);
                    return $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='$addsurat' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-ui-add'></i></a>
                        </div>
                        ";
                })
                ->addColumn('file', function ($row) {
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id',$row->id)->get();

                    foreach($dataizin as $izin){
                        $printsurat =  route('admin.printizin', $izin->id);
                    
                        if ($row->id == $izin->id_attendance) {
                            $actionBtn = "
                            <div class='d-block text-center'>
                                <a href='$printsurat' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-download-alt'></i></a>
                            </div>
                            ";
                            return $actionBtn;
                           
                        } else {
                            $actionBtn = "";
                            return $actionBtn;
                        }
                    }    
                })

                ->addColumn('status', function ($row) {
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id',$row->id)->get();

                    foreach($dataizin as $izin){
                        if ($row->id == $izin->id_attendance) {
                            if ($row->approval == 1) {
                                $apprv = '<span class="badge badge-success">Disetujui</span>';
                            } else {
                                $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                            }
                            return $apprv;
                        }else{
                            return $apprv ='';
                        }
                    }

                    
                })
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang', 'action','file','status'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function createizinkehadiran($id)
    {

        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->where('attendance.id', $id)->first();
        return view('admin.createizinkehadiran', compact('data'));
    }

    public function storeizinkehadiran(Request $request)
    {

        if ($request->validasi == NULL) {
            return redirect()->route('admin.createizinkehadiran', $request->id_izin)->with('error', 'Validasi Tidak diisi!');
        } else {
            Izin::insert([
                'id_attendance' => $request->id_attendance,
                'nopeg' => $request->nopeg,
                'name' => $request->name,
                'unit' => $request->unit,
                'tgl_awal_izin' => date('Y-m-d H:i:s', strtotime($request->tgl_awal_izin)),
                'tgl_akhir_izin' => date('Y-m-d H:i:s', strtotime($request->tgl_akhir_izin)),

                'alasan' => $request->alasan,
                'validasi' => $request->validasi,
            ]);

            $dataqr = Izin::where('nopeg', $request->nopeg)->first();
            $qrcode_filename = 'qr-' . base64_encode($request->nopeg . date('Y-m-d H:i:s')) . '.svg';
            // dd($qrcode_filename);
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->nopeg . '-' . $request->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

            QR::where('nopeg', $request->nopeg)->insert([
                'id_attendance' => $request->id_attendance,
                'nopeg' => $request->nopeg,
                'qr_peg' => $qrcode_filename,
            ]);

            return redirect()->route('admin.datapresensi')->with('success', 'Pengajuan Izin Berhasil!');
        }
    }

    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();
        $dataqr = QR::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.printizin', compact('data', 'dataqr'))->setPaper('A5','landscape');
        return $pdf->stream();
    }
    //END DATA PRESENSI


    //DATA REKAPITULASI
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

                ->addColumn('duration', function ($data) {

                    $durasi_telat = strtotime('00:00:00');
                    $data_att     = Attendance::where('nip', $data->nopeg)->get();
                    foreach ($data_att as $row) {
                        if (date("H:i:s", strtotime($row->jam_masuk)) > $data->awal_tugas && $row->hari != '6') {
                            $durasitelat = strtotime($row->jam_masuk) - strtotime($data->awal_tugas);
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
                
                ->addColumn('izin',function($data){
                    $izin = IzinKerja::selectRaw('SUM(total_izin)*8 AS total, nopeg')->where('nopeg',$data->nopeg)->get();

                    foreach($izin as $row){
                        if($row->nopeg != NULL){
                            return $row->total.' Jam';
                        }else{
                            return '';
                        }
                    }
                })

                ->addColumn('detail',function($data){
                    $rekap =  route('admin.detailrekap', $data->nopeg);
                    $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='$rekap' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-eye-alt'></i></a>
                        </div>";
                    return $actionBtn;
                })
                ->rawColumns(['duration','izin','detail'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function detailrekap($nip)
    {

        $data = DB::select('CALL getTotalTelatPerBulan(' . $nip . ')');
        
        $dataizinkerja = Attendance::selectRaw('izin_kerja.*,attendance.id, attendance.nip, MONTH(izin_kerja.tgl_awal_izin) AS bulan,  YEAR(izin_kerja.tgl_awal_izin) AS tahun, izin_kerja.total_izin AS total')
        ->join('izin_kerja','attendance.nip','=','izin_kerja.nopeg')
        ->whereIn('attendance.nip',[$nip])
        ->whereNotIn('izin_kerja.jenis_izin',['9'])
        ->groupBy('bulan', 'tahun')
        ->get();

        $datasakit = Attendance::selectRaw('izin_kerja.*, attendance.id, attendance.nip, MONTH(izin_kerja.tgl_awal_izin) AS bulan,  YEAR(izin_kerja.tgl_awal_izin) AS tahun, izin_kerja.total_izin AS total')
        ->join('izin_kerja','attendance.nip','=','izin_kerja.nopeg')
        ->where('attendance.nip',$nip)
        ->where('izin_kerja.jenis_izin','9')
        ->groupBy('bulan', 'tahun')
        ->get();

        return view('admin.detailrekap',compact('data','dataizinkerja','datasakit'));
    }

    //END DATA REKAPITULASI


    //DATA IZIN KARYAWAN

    public function dataizin()
    {
        return view('admin.dataizin');
    }

    public function listizin(Request $request)
    {
        $data = DB::table('izin_kerja')->join('jenis_izin','jenis_izin.id_izin','=','izin_kerja.jenis_izin');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('print', function ($row) {
                    $printsurat =  route('admin.printizinkerja', $row->id_izinkerja);
                    $actionBtn = "
                    <div class='d-block text-center'>
                        <a href='$printsurat' class='btn btn btn-success align-items-center'><i class='icofont icofont-download-alt'></i></a>
                    </div>
                    ";
                    return $actionBtn;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['print','status'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function createizin()
    {
        $datauser = User::groupby('nopeg')->get();
        $jenisizin = JenisIzin::get();
        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->get();
        return view('admin.createizin', compact('data', 'datauser','jenisizin'));
    }

    public function storeizin(Request $request)
    {
        // dd(date('d-m-Y', strtotime($request->startDate)) );
        if ($request->total > $request->lama_izin && $request->jenis_izin != 'Sakit') {
            return redirect()->route('admin.dataizin')->with('error', 'Pengajuan Izin Tidak Berhasil, Total lama izin melebihi ketentuan hari yang diizinkan!');
        } elseif ($request->validasi == NULL) {
            return redirect()->route('admin.createizin')->with('error', 'Validasi Tidak diisi!');
        } else {
            IzinKerja::insert([
                'nopeg' => explode('-', $request->nopeg)[0] ,
                'name' =>  explode('-', $request->nopeg)[1],
                'unit' =>  explode('-', $request->nopeg)[2],
                'jenis_izin' => $request->jenis_izin,
                'total_izin' => $request->total,
                'tgl_awal_izin' => date('Y-m-d', strtotime($request->startDate)),
                'tgl_akhir_izin' => date('Y-m-d', strtotime($request->endDate)),
                'validasi' => $request->validasi,
                'approval' => '0',
            ]);
        }

        return redirect()->route('admin.dataizin')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizinkerja($id)
    {
        $data = IzinKerja::where('id_izinkerja', $id)->first();
        $jenisizin = JenisIzin::where('jenis_izin', '!=', 'sakit')->get();

        $pdf = PDF::loadview('admin.printizinkerja', compact('data', 'jenisizin'))->setPaper('potrait');
        return $pdf->stream();
    }

    //END DATA IZIN KARYAWAN


    //DATA CUTI KARYAWAN

    public function datacuti()
    {
        return view('admin.datacuti');
    }

    public function listcuti(Request $request)
    {
        $data = DB::table('cuti');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $printsurat =  route('admin.printcuti', $row->id_cuti);
                    $actionBtn = "
                    <div class='d-block text-center'>
                        <a href='$printsurat' class='btn btn btn-success align-items-center'><i class='icofont icofont-download-alt'></i></a>
                    </div>
                    ";
                    return $actionBtn;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }


    public function createcuti()
    {
        $datauser = User::groupby('nopeg')->get();
        $jeniscuti = JenisCuti::get();
        // $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas')->join('users','attendance.nip','=','users.nopeg')->get();
        return view('admin.createcuti', compact('datauser', 'jeniscuti'));
    }

    public function storecuti(Request $request)
    {
        Cuti::insert([
            'nopeg' => explode('-', $request->nopeg)[0] ,
            'name' =>  explode('-', $request->nopeg)[1],
            'unit' =>  explode('-', $request->nopeg)[2],
            'jenis_cuti' => $request->jenis_cuti,
            'tgl_awal_cuti' => date('Y-m-d', strtotime($request->startDate)),
            'tgl_akhir_cuti' => date('Y-m-d', strtotime($request->endDate)),
            'total_cuti' => $request->total,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'tgl_pengajuan' => Carbon::now()->toDateTimeString(),
            'validasi' => $request->validasi,
        ]);

        return redirect()->route('admin.datacuti')->with('success', 'Pengajuan cuti Berhasil!');
    }

    public function printcuti($id)
    {
        $data = Cuti::where('id_cuti', $id)->first();

        $pdf = PDF::loadview('admin.printcuti', compact('data'))->setPaper('potrait');
        return $pdf->stream();
    }

    //ENDDATA CUTI KARYAWAN



}
