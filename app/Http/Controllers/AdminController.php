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
        $user = User::where('fungsi', 'Admin')->get();
        $attendance = Attendance::select('tanggal')->groupby('tanggal')->get();
        // dd($attendance[1]);
        return view('admin.datapresensi', compact('user', 'attendance'));
    }


    public function listkaryawan(Request $request)
    {
        $data = Attendance::selectRaw('attendance.*, users.name, users.awal_tugas, users.akhir_tugas')
            ->join('users', 'attendance.nip', '=', 'users.nopeg')
            ->where('fungsi', 'Admin')
            ->where('attendance.nip', $request->get('filter1'), '', 'and')
            ->where('attendance.tanggal', $request->get('filter2'), '', 'and')
            ->orderby('attendance.tanggal', 'asc');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('hari', function ($row) {
                    return config('app.days')[$row->hari];
                })
                ->addColumn('duration', function ($row) {
                    if ($row->jam_masuk == NULL && $row->jam_siang == NULL && $row->jam_pulang != NULL) {
                        $durationwork = date('00:00:00');
                    } else if ($row->jam_masuk == NULL && $row->jam_siang != NULL && $row->jam_pulang == NULL) {
                        $durationwork = date('00:00:00');
                    } else if ($row->jam_masuk != NULL && $row->jam_siang == NULL && $row->jam_pulang == NULL) {
                        $durationwork = date('00:00:00');
                    } else if ($row->jam_masuk == NULL && $row->jam_siang != NULL && $row->jam_pulang != NULL) {
                        $duration = strtotime($row->jam_pulang) - strtotime($row->jam_siang);
                        $durationwork = date("H:i:s", $duration);
                    } else if ($row->jam_masuk != NULL && $row->jam_siang == NULL && $row->jam_pulang != NULL) {
                        if ($row->hari == '5') {
                            $duration = strtotime("13:30:00") - strtotime($row->jam_masuk);
                            $durationwork = date("H:i:s", $duration);
                        } else {
                            $duration = strtotime("13:00:00") - strtotime($row->jam_masuk);
                            $durationwork = date("H:i:s", $duration);
                        }
                    } else if ($row->jam_masuk != NULL && $row->jam_siang != NULL && $row->jam_pulang == NULL) {
                        $duration = strtotime($row->jam_siang) - strtotime($row->jam_masuk);
                        $durationwork = date("H:i:s", $duration);
                    } else {
                        $duration = strtotime($row->jam_pulang) - strtotime($row->jam_masuk);
                        $durationwork = date("H:i:s", $duration);
                    }
                    return $durationwork;
                })

                ->addColumn('latemasuk', function ($row) {
                    $durasi = '';
                    if ($row->hari != 6 && $row->hari != 0) {
                        if ($row->jam_masuk == NULL && $row->jam_siang != NULL) {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('08:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if ($row->jam_masuk == NULL && $row->jam_siang == NULL) {
                            $durasitelat = strtotime('12:45:00') - strtotime('08:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if (date("H:i:s", strtotime($row->jam_masuk)) > '08:00:00') {
                            $durasitelat = strtotime($row->jam_masuk) - strtotime('08:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        }
                    }
                    return $durasi;
                })

                ->addColumn('latesiang', function ($row) {
                    $durasi = '';
                    if ($row->hari == 5) {
                        if ($row->jam_siang == NULL && $row->jam_pulang != NULL) {
                            $durasitelat = strtotime($row->jam_pulang) - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if ($row->jam_siang == NULL && $row->jam_pulang == NULL) {
                            $durasitelat = strtotime('16:59:00') - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if (date("H:i:s", strtotime($row->jam_siang)) > '13:30:00') {
                            $durasitelat = strtotime($row->jam_pulang) - strtotime('13:30:00');
                            $durasi = date("H:i:s", $durasitelat);
                        }
                    } else if ($row->hari != 6 && $row->hari != 0) {
                        if ($row->jam_siang == NULL && $row->jam_pulang != NULL) {
                            $durasitelat = strtotime($row->jam_pulang) - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if ($row->jam_siang == NULL && $row->jam_pulang == NULL) {
                            $durasitelat = strtotime('16:59:00') - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        } else if (date("H:i:s", strtotime($row->jam_siang)) > '13:00:00') {
                            $durasitelat = strtotime($row->jam_siang) - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelat);
                        }
                    }
                    return $durasi;
                })
                ->addColumn('action', function ($row) {
                    // $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    // if ($workingdays < 3) {
                    return getAksi($row->id, 'att');
                    // }else{
                    //     return '-';

                    // }
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
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang', 'latesore', 'action', 'file', 'status'])

                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function editAtt($id)
    {
        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas, unit.nama_unit')
            ->join('users', 'attendance.nip', '=', 'users.nopeg')
            ->join('unit', 'unit.id', '=', 'users.unit')
            ->where('attendance.id', $id)->first();
        return response()->json($data);
    }

    public function storeizinkehadiran(Request $request)
    {
        Izin::insert([
            'id_attendance' => $request->id,
            'nopeg' => $request->nip,
            'name' => $request->name,
            'unit' => $request->unit,
            'tanggal' => $request->tanggall,
            'jam_awal' => $request->jam_awal,
            'jam_akhir' => $request->jam_akhir,
            'alasan' => $request->alasan,
            'validasi' => 1,
            'approval' => 0,
        ]);

        // $dataqr = Izin::where('nopeg', $request->nopeg)->first();
        // $qrcode_filename = 'qr-' . base64_encode($request->nopeg . date('Y-m-d H:i:s')) . '.svg';
        // // dd($qrcode_filename);
        // QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->nopeg . '-' . $request->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

        // QR::where('nopeg', $request->nopeg)->insert([
        //     'id_attendance' => $request->id_attendance,
        //     'nopeg' => $request->nopeg,
        //     'qr_peg' => $qrcode_filename,
        // ]);

        return redirect()->route('admin.datapresensi')->with('success', 'Pengajuan Izin Berhasil!');
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

                    $data_att = DB::select('CALL getTotalTelatPerBulan(' . $data->nopeg . ')');
                    $pagi = 0;
                    $siang = 0;
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


    function getWorkingDays($startDate, $endDate)
    {
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);
        $curentYear = date('Y', $begin);
        $endYear = date('Y', $end);
        $libur_nasional = DB::table('libur_nasional')->whereYear('tanggal', '=', $curentYear)->whereYear('tanggal', '=', $endYear)->get();
        if ($begin > $end) {
            return 0;
        } else {
            $no_days  = 0;
            $weekends = 0;
            while ($begin <= $end) {
                $no_days++; // no of days in the given interval
                $what_day = date("N", $begin);
                if ($what_day > 5) { // 6 and 7 are weekend days
                    $weekends++;
                }
                // cek libur nasional
                foreach ($libur_nasional as $key => $value) {
                    if (date('Y-m-d', $begin) == $value->tanggal) {
                        $weekends++;
                    }
                }
                $begin += 86400; // +1 day
            };

            $working_days = $no_days - $weekends;

            return response()->json($working_days);
        }
    }


    //DATA IZIN KARYAWAN

    public function dataizin()
    {
        $user = User::join('unit', 'users.unit', '=', 'unit.id')->where('fungsi', 'admin')->get();
        $jenisizin = JenisIzin::all();

        $data = [
            'user' => $user,
            'jenisizin' => $jenisizin
        ];

        return view('admin.dataizin', compact('data'));
    }

    public function listizin(Request $request)
    {
        $data = DB::table('izin_kerja')->join('unit', 'izin_kerja.unit', '=', 'unit.id')->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')->orderBy('izin_kerja.created_at')->get();

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
    }

    public function storeizin(Request $request)
    {

        $qrcode_filename = 'qr-' . explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_izin)[0] . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

        IzinKerja::insert([
            'nopeg' => explode('-', $request->nopeg)[0],
            'name' =>  explode('-', $request->nopeg)[1],
            'unit' =>  explode('-', $request->nopeg)[2],
            'jenis_izin' => explode('|', $request->jenis_izin)[0],
            'total_izin' => $request->total_izin,
            'tgl_awal_izin' => date('Y-m-d', strtotime($request->tgl_awal_izin)),
            'tgl_akhir_izin' => date('Y-m-d', strtotime($request->tgl_akhir_izin)),
            'tgl_pengajuan' => Carbon::now()->toDateTimeString(),
            'validasi' => '1',
            'approval' => '0',
            'qrcode_peg' => $qrcode_filename,
        ]);

        return redirect()->route('admin.dataizin')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizinkerja($id)
    {
        $data = IzinKerja::join('users', 'izin_kerja.nopeg', '=', 'users.nopeg')->join('unit', 'izin_kerja.unit', '=', 'unit.id')->where('id_izinkerja', $id)->first();
        $kepala = User::select('name')->where('nopeg', $data->atasan)->first();
        $jenisizin = JenisIzin::where('jenis_izin', '!=', 'sakit')->get();
        // $dataqr = QR::where('id_izinkerja', $id)->first();

        $pdf = PDF::loadview('admin.printizinkerja', compact('data', 'jenisizin', 'kepala'))->setPaper('potrait');
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
        $user = User::join('unit', 'users.unit', '=', 'unit.id')->where('fungsi', 'admin')->get();
        $jeniscuti = JenisCuti::all();

        $data = [
            'user' => $user,
            'jeniscuti' => $jeniscuti
        ];
        return view('admin.datacuti', compact('data'));
    }

    public function historycuti($nopeg, $jenis)
    {
        $history_cuti =
            cuti::join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('cuti.nopeg', $nopeg)
            ->where('cuti.jenis_cuti', $jenis)
            ->GROUPBY('cuti.jenis_cuti')->sum('total_cuti');

        return response()->json($history_cuti);
    }

    public function listcuti(Request $request)
    {
        $data = cuti::join('unit', 'cuti.unit', 'unit.id')->join('jenis_cuti', 'cuti.jenis_cuti', 'jenis_cuti.id_jeniscuti')->orderby('unit.created_at')->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return getAksi($row->id_cuti, 'cuti');
                })
                ->addColumn('status', function ($row) {
                    return getAprv($row->id_cuti, 'cuti', $row->alasan_tolak);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function storecuti(Request $request)
    {

        $qrcode_filename = 'qr-' . explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_cuti)[0] . '.svg';
        // dd($qrcode_filename);
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filename));

        Cuti::insert([
            'nopeg' => explode('-', $request->nopeg)[0],
            'name' =>  explode('-', $request->nopeg)[1],
            'unit' =>  explode('-', $request->nopeg)[2],
            'jenis_cuti' => explode('-', $request->jenis_cuti)[0],
            'tgl_awal_cuti' => date('Y-m-d', strtotime($request->tgl_awal_cuti)),
            'tgl_akhir_cuti' => date('Y-m-d', strtotime($request->tgl_akhir_cuti)),
            'total_cuti' => $request->total_cuti,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'tgl_pengajuan' => Carbon::now()->toDateTimeString(),
            'validasi' => 1,
            'approval' => 0,
            'qrcode_peg' => $qrcode_filename,
        ]);

        return redirect()->route('admin.datacuti')->with('success', 'Add Data Berhasil!');
    }

    public function printcuti($id)
    {
        $data = Cuti::join('unit', 'cuti.unit', '=', 'unit.id')->join('jenis_cuti', 'cuti.jenis_cuti', '=', 'jenis_cuti.id_jeniscuti')->where('id_cuti', $id)->first();

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

    public function liburnasional()
    {
        $libur = LiburNasional::get();
        return view('admin.liburnasional', compact('libur'));
    }


    public function listlibur(Request $request)
    {
        $data = LiburNasional::get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return getAksi($data->id, 'liburnasional');
                })

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function editlibur($id)
    {
        $libur = LiburNasional::where('id', $id)->first();
        return response()->json($libur);
    }

    public function updatelibur(Request $request)
    {
        LiburNasional::where('id', $request->id)->update([
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
