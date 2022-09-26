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
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\LiburNasional;
use Carbon\Carbon;
use App\Models\QR;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    //DASHBOARD
    public function index()
    {
        return view('admin.admin_v');
    }

    //END DASHBOARD

    //DATA PRESENSI
    public function datapresensi()
    {
        // $history_cuti = Cuti::select('cuti.*, users.sisacuti, sum(cuti.total_cuti) AS total_cuti')
        // ->join('users','users.nopeg','=','cuti.nopeg')
        // ->join('jenis_cuti','cuti.jenis_cuti', 'jenis_cuti.jenis_cuti')
        // ->where('cuti.nopeg',1803)->first();
        // // $test = getWorkingDays('2022-08-12', '2022-08-18');
        // dd($history_cuti);
        return view('admin.datapresensi');
    }


    public function listkaryawan(Request $request)
    {
        $data = Attendance::selectRaw('attendance.*, users.name, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function ($row){
                    return $row->nip.'-'.$row->name;
                })
                ->addColumn('duration', function ($row) {
                    if ($row->jam_pulang == NULL) {
                        $duration = strtotime('13:00:00') - strtotime($row->jam_masuk);
                        $durationwork = date("H:i:s", $duration);
                        return $durationwork;
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
                    if ($row->jam_masuk == NULL) {
                        $durasitelat = strtotime('13:15:00') - strtotime('08:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    } else if ($row->hari != 6 && $row->hari != 0 && date("H:i:s", strtotime($row->jam_masuk)) <= '08:00:00') {
                        return '';
                    } else if ($row->hari != 6 && $row->hari != 0 && date("H:i:s", strtotime($row->jam_masuk)) > '08:00:00') {
                        $durasitelat = strtotime($row->jam_masuk) - strtotime('08:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    }
                })
                ->addColumn('latesiang', function ($row) {
                    if ($row->hari == 5) {
                        if ($row->jam_siang == NULL) {
                            $durasitelat = strtotime('17:00:00') - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        } else if (date("H:i:s", strtotime($row->jam_siang)) <= '13:15:00') {
                            return '';
                        } else if (date("H:i:s", strtotime($row->jam_siang)) > '13:30:00') {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        }
                    } else {
                        if ($row->hari != 6 && $row->hari != 0 && $row->jam_siang == NULL) {
                            $durasitelat = strtotime('17:00:00') - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        } else if ($row->hari != 6 && $row->hari != 0 && date("H:i:s", strtotime($row->jam_siang)) <= '12:45:00') {
                            return '';
                        } else if ($row->hari != 6 && $row->hari != 0 && date("H:i:s", strtotime($row->jam_siang)) > '13:00:00') {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                            return $durasi;
                        }
                    }
                })
                ->addColumn('latesore',function($row) {
                    if ($row->hari != 6 && $row->hari != 0 && $row->jam_pulang == NULL){
                        $durasitelat = strtotime('17:00:00') - strtotime('15:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    } else if($row->jam_pulang < strtotime('17:00:00') && $row->hari != 6 && $row->hari != 7 ){
                        $durasiplg = strtotime($row->jam_pulang) - strtotime('17:00:00');
                        $durasi = date("H:i:s", $durasiplg);
                        return $durasi;
                    }

                })

                ->addColumn('action', function ($row) {
                    $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    if ($workingdays < 3) {
                        $addsurat = route('admin.createizinkehadiran', $row->id);
                        return $actionBtn = "
                        <div class='d-block text-center'>
                        <a href='$addsurat' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-ui-add'></i></a>
                        </div>
                        ";
                    }else{
                        return '-';

                    }
                })
                ->addColumn('file', function ($row) {
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id', $row->id)->get();

                    foreach ($dataizin as $izin) {
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
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id', $row->id)->get();

                    foreach ($dataizin as $izin) {
                        if ($row->id == $izin->id_attendance) {
                            if ($row->approval == 1) {
                                $apprv = '<span class="badge badge-success">Disetujui</span>';
                            } else {
                                $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                            }
                            return $apprv;
                        } else {
                            return $apprv = '';
                        }
                    }
                })
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang','latesore', 'action','file','status'])

                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function createizinkehadiran($id)
    {

        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas, unit.nama_unit')
            ->join('users', 'attendance.nip', '=', 'users.nopeg')
            ->join('unit', 'unit.id', '=', 'users.unit')
            ->where('attendance.id', $id)->first();
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
                'unit' => $request->idunit,
                'tanggal' => $request->tgl,
                'jam_awal' => date('H:i:s', strtotime($request->jam_awal)),
                'jam_akhir' => date('H:i:s', strtotime($request->jam_akhir)),
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

        $pdf = PDF::loadview('admin.printizin', compact('data', 'dataqr'))->setPaper('A5', 'landscape');
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

                    $data_att     = DB::select('CALL getTotalTelatPerBulan('.$data->nopeg.')');
                    $pagi = 0;
                    $siang = 0;
                    $total = 0;
                    foreach ($data_att as $row) {
                        $pagi += strtotime($row->total_telat_pagi);
                        $siang += strtotime($row->total_telat_siang);
                        $total = $pagi + $siang;
                    }
                    return (date("H:i:s", $total));
                })

                ->addColumn('izin', function ($data) {
                    $izin = IzinKerja::selectRaw('SUM(total_izin)*8 AS total, nopeg')->where('nopeg', $data->nopeg)->get();

                    foreach ($izin as $row) {
                        if ($row->nopeg != NULL) {
                            return $row->total . ' Jam';
                        } else {
                            return '';
                        }
                    }
                })

                ->addColumn('detail', function ($data) {
                    $rekap =  route('admin.detailrekap', $data->nopeg);
                    $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='$rekap' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-eye-alt'></i></a>
                        </div>";
                    return $actionBtn;
                })
                ->rawColumns(['duration', 'izin', 'detail'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function detailrekap($nip)
    {

        $data = DB::select('CALL getTotalTelatPerBulan(' . $nip . ')');

        $dataizinkerja = Attendance::selectRaw('izin_kerja.*,attendance.id, attendance.nip, MONTH(izin_kerja.tgl_awal_izin) AS bulan,  YEAR(izin_kerja.tgl_awal_izin) AS tahun, izin_kerja.total_izin AS total')
            ->join('izin_kerja', 'attendance.nip', '=', 'izin_kerja.nopeg')
            ->whereIn('attendance.nip', [$nip])
            ->whereNotIn('izin_kerja.jenis_izin', ['9'])
            ->groupBy('bulan', 'tahun')
            ->get();

        $datasakit = Attendance::selectRaw('izin_kerja.*, attendance.id, attendance.nip, MONTH(izin_kerja.tgl_awal_izin) AS bulan,  YEAR(izin_kerja.tgl_awal_izin) AS tahun, izin_kerja.total_izin AS total')
            ->join('izin_kerja', 'attendance.nip', '=', 'izin_kerja.nopeg')
            ->where('attendance.nip', $nip)
            ->where('izin_kerja.jenis_izin', '9')
            ->groupBy('bulan', 'tahun')
            ->get();

        return view('admin.detailrekap', compact('data', 'dataizinkerja', 'datasakit'));
    }

    //END DATA REKAPITULASI


    //DATA IZIN KARYAWAN

    public function dataizin()
    {
        return view('admin.dataizin');
    }

    public function listizin(Request $request)
    {
        $data = DB::table('izin_kerja')->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('print', function ($row) {
                    return getAksi($row->id_izinkerja, 'izin');
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['print', 'status'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function createizin()
    {
        $datauser = User::groupby('nopeg')->get();
        $jenisizin = JenisIzin::get();
        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas, users.atasan')->join('users', 'attendance.nip', '=', 'users.nopeg')->get();
        return view('admin.createizin', compact('data', 'datauser', 'jenisizin'));
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
                'nopeg' => explode('-', $request->nopeg)[0],
                'name' =>  explode('-', $request->nopeg)[1],
                'unit' =>  explode('-', $request->nopeg)[2],
                'jenis_izin' => $request->jenis_izin,
                'total_izin' => $request->total,
                'tgl_awal_izin' => date('Y-m-d', strtotime($request->startDate)),
                'tgl_akhir_izin' => date('Y-m-d', strtotime($request->endDate)),
                'validasi' => $request->validasi,
                'approval' => '0',
            ]);

            $dataqr = IzinKerja::where('id_izinkerja', $request->id_izinkerja)->first();
            $qrcode_filename = 'qr-' . base64_encode(explode('-', $request->nopeg)[0] . date('Y-m-d H:i:s')) . '.svg';
            // dd($qrcode_filename);
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

            QR::where('id_izinkerja', $request->id_izinkerja)->update([
                'qr_peg' => $qrcode_filename,
            ]);
        }

        return redirect()->route('admin.dataizin')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizinkerja($id)
    {
        $data = IzinKerja::join('users', 'izin_kerja.nopeg', '=', 'users.nopeg')->where('id_izinkerja', $id)->first();
        $kepala = User::select('name')->where('nopeg', $data->atasan)->first();
        $jenisizin = JenisIzin::where('jenis_izin', '!=', 'sakit')->get();
        $dataqr = QR::where('id_izinkerja', $id)->first();


        $pdf = PDF::loadview('admin.printizinkerja', compact('data', 'jenisizin', 'dataqr', 'kepala'))->setPaper('potrait');
        return $pdf->stream();
    }

    public function batal_izin($id)
    {
        $delete = IzinKerja::where('id_izinkerja', $id)->delete();
        if ($delete) {
            return redirect()->back()->with('success', 'Berhasil membatalkan izin');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan izin');
        }
    }


    //END DATA IZIN KARYAWAN


    //DATA CUTI KARYAWAN

    public function datacuti()
    {
        // $history_cuti = DB::table('jenis_cuti')->select("jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari")
        // ->leftjoin('cuti','jenis_cuti.id_jeniscuti','=','cuti.jenis_cuti')
        // ->where('cuti.nopeg','1803')
        // ->groupby('cuti.jenis_cuti')
        // ->get();
        // dd($history_cuti);
        return view('admin.datacuti');
    }

    public function listcuti(Request $request)
    {
        $data = DB::table('cuti');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return getAksi($row->id_cuti, 'cuti');
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['action', 'status'])
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
        $max = explode('-', $request->jenis_cuti)[1];
       if($request->total > $max ){
            return redirect()->back()->with('error', 'Melebihi Batas Hari Cuti');

       }else{
        Cuti::insert([
            'nopeg' => explode('-', $request->nopeg)[0] ,
            'name' =>  explode('-', $request->nopeg)[1],
            'unit' =>  explode('-', $request->nopeg)[2],
            'jenis_cuti' => explode('-', $request->jenis_cuti)[0],
            'tgl_awal_cuti' => date('Y-m-d', strtotime($request->startDate)),
            'tgl_akhir_cuti' => date('Y-m-d', strtotime($request->endDate)),
            'total_cuti' => $request->total,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'tgl_pengajuan' => Carbon::now()->toDateTimeString(),
            'validasi' => $request->validasi,
        ]);
       }
    
       return redirect()->route('admin.datacuti')->with('success', 'Add Data Berhasil!');

    }

    public function printcuti($id)
    {
        $data = Cuti::where('id_cuti', $id)->first();

        $pdf = PDF::loadview('admin.printcuti', compact('data'))->setPaper('potrait');
        return $pdf->stream();
    }

    public function batal_cuti($id)
    {
        $delete = Cuti::where('id_cuti', $id)->delete();
        if ($delete) {
            return redirect()->back()->with('success', 'Berhasil membatalkan izin');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan izin');
        }
    }

    //ENDDATA CUTI KARYAWAN

    //Libur Nasional 

    public function liburnasional(){
        $libur = LiburNasional::get();
        return view('admin.liburnasional',compact('libur'));
    }

    
    public function listlibur(Request $request)
    {
        $data = LiburNasional::get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $delete_url = route('admin.destroylibur', $data->id);

                    return $actionBtn = "
                    <div class='d-block text-center'>
                    <a href='javascript:void(0)' data-toggle='tooltip' class='btn btn btn-warning btn-xs align-items-center editLibur' 
                    data-id='$data->id' data-original-title='Edit'><i class='icofont icofont-edit-alt'></i></a>
                    <a href='$delete_url' class='btn btn-sm btn-danger btn-xs align-items-center''><i class='icofont icofont-trash'></i></a>
                    </div>
                    "; 
                })

                ->rawColumns(['action'])
                ->make(true);
        }

    }

    public function editlibur($id)
    {
        $libur = LiburNasional::where('id',$id)->first();   
        return response()->json($libur);
    }

    public function updatelibur(Request $request)
    {
        LiburNasional::where('id',$request->id)->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.liburnasional')->with('success', 'Edit Data Berhasil!');
    }

    public function createlibur(Request $request)
    {
        LiburNasional::create([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.liburnasional')->with('success', 'Add Data Berhasil!');
    }

    public function destroylibur($id)
    {
        LiburNasional::where('id', $id)->delete();
        return redirect()->route('admin.liburnasional')->with('success', 'Data berhasil dihapus!');
    }

}
