<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalSatpamCalendarResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Attendance;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\IzinKerja;
use App\Models\JadwalSatpam;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\LiburNasional;
use App\Models\Jabatan;
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
        $data = Attendance::selectRaw('attendance.*, users.name')
            ->join('users', 'attendance.nip', '=', 'users.nopeg')
            ->where('users.status', '1')
            ->where('attendance.nip', $request->get('filter1'), '', 'and')
            ->where('attendance.tanggal', $request->get('filter2'), '', 'and')
            ->orderby('attendance.tanggal', 'asc');
        // dd(strtotime($data->get()[0]->jam_pulang) - strtotime($data->get()[0]->jam_masuk));
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
                        $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_pulang);
                        $awal = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_siang);
                        $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
                    } else if ($row->jam_masuk != NULL && $row->jam_siang == NULL && $row->jam_pulang != NULL) {
                        if ($row->hari == '5') {
                            $akhir = Carbon::createFromTimestamp(strtotime("13:00:00"));
                            $awal = Carbon::createFromTimestamp(strtotime($row->jam_masuk));
                            $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
                        } else {
                            $akhir = Carbon::createFromTimestamp(strtotime("13:30:00"));
                            $awal = Carbon::createFromTimestamp(strtotime($row->jam_masuk));
                            $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
                        }
                    } else if ($row->jam_masuk != NULL && $row->jam_siang != NULL && $row->jam_pulang == NULL) {
                        $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_siang);
                        $awal = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_masuk);
                        $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
                    } else {
                        $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_pulang)->subHour(1);
                        $awal = Carbon::createFromFormat("Y-m-d H:i:s", $row->jam_masuk);
                        $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
                    }
                    return $durationwork;
                })

                ->addColumn('latemasuk', function ($row) {
                    if (Carbon::createFromTimestamp(strtotime($row->jam_masuk))->format('%H:%I:%S') > Carbon::createFromTimestamp(strtotime("08:00:00"))->format('%H:%I:%S')) {
                        $akhir = Carbon::createFromTimestamp(strtotime("08:033:00"));
                        $awal = Carbon::createFromTimestamp(strtotime("08:00:00"));
                        $durasi = $akhir->diff($awal)->format('%H:%I:%S');
                    } else {
                        return 'kosong';
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
                ->addColumn('note', function ($row) {
                    if ($row->status == 0) {
                        $note = 'Kurang';
                    } else {
                        $note = 'Lengkap';
                    }
                    return $note;
                })
                ->addColumn('action', function ($row) {
                    // $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    // if ($workingdays < 3) {
                    return getAksi($row->id, 'att');
                    // }else{
                    //     return '-';

                    // }
                })
                ->addColumn('status', function ($row) {
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id', $row->id)->get();

                    foreach ($dataizin as $izin) {
                        if ($row->id == $izin->id_attendance) {
                            if ($row->approval == 1) {
                                $apprv = '<span class="badge badge-success">Disetujui Atasan Langsung</span>';
                            } else {
                                $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                            }
                            return $apprv;
                        } else {
                            return $apprv = '';
                        }
                    }
                })
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang', 'latesore', 'action', 'status', 'note'])

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

        $qrcode_filenamepeg = 'qr-' . $request->nip . '-' . $request->id . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->nip . '-' . $request->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
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
            'qrcode_peg' => $qrcode_filenamepeg,
        ]);

        return redirect()->route('admin.datapresensi')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.printizin', compact('data'))->setPaper('A5', 'landscape');
        return $pdf->stream();
    }
    //END DATA PRESENSI

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
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->where('fungsi', 'admin')->get();
        // dd($user[20]);
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
                    return getAprv($row->id_izinkerja, 'izin', '');
                })
                ->rawColumns(['print', 'status'])
                ->make(true);
        }
    }

    public function storeizin(Request $request)
    {
        $qrcode_filenamepeg = 'qr-' . explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_izin)[0] . '.svg';
        $qrcode_filenameat = 'qr-' . $request->atasan . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_izin)[0] . '.svg';
        if (auth()->user()->role == 'admin_bsdm') {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan . '-' .  $request->name_jab . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));
        } else {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
        }

        if (auth()->user()->role == 'admin_bsdm') {
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
                'approval' => '1',
                'qrcode_peg' => $qrcode_filenamepeg,
                'qrcode_kepala' => $qrcode_filenameat,
            ]);
        } else {
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
                'qrcode_peg' => $qrcode_filenamepeg,
            ]);
        }


        return redirect()->route('admin.dataizin')->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function printizinkerja($id)
    {
        $data = IzinKerja::join('users', 'izin_kerja.nopeg', '=', 'users.nopeg')->join('unit', 'izin_kerja.unit', '=', 'unit.id')->where('id_izinkerja', $id)->first();
        // $atasan = User::selectRaw('jabatan.*')->join('jabatan', 'jabatan.id', 'users.atasan')->where('jabatan.nopeg', $data->atasan)->first();
        $atasan = Jabatan::join('users', 'users.atasan', '=', 'jabatan.id')->where('users.atasan', $data->atasan)->first();
        $jenisizin = JenisIzin::where('jenis_izin', '!=', 'sakit')->get();

        $pdf = PDF::loadview('admin.printizinkerja', compact('data', 'jenisizin', 'atasan'))->setPaper('potrait');
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
        // $user = User::join('unit', 'users.unit', '=', 'unit.id')->where('fungsi', 'admin')->get();
        $user = User::SelectRaw('users.*,unit.*, jb1.nopeg as peg_jab, jb1.nama as name_jab, jb2.nopeg as peg_jab2, jb2.nama as name_jab2')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan as jb1', 'users.atasan', '=', 'jb1.id')
            ->join('jabatan as jb2', 'users.atasan_lang', '=', 'jb2.id')
            ->where('fungsi', 'admin')->get();

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

    public function datacuti_show($id)
    {
        $cuti = Cuti::with(['jeniscuti', 'unit', 'user'])->where('id_cuti', $id)->first();
        $jeniscuti = JenisCuti::where('id_jeniscuti', $cuti->jenis_cuti)->first();
        $datauser = User::where('fungsi', 'satpam')->get();
        // dd($cuti);
        return view('admin.datacuti_show', compact('cuti', 'jeniscuti', 'datauser'));
    }

    public function datacuti_calendar($id, $nopeg)
    {
        $cuti = Cuti::with(['user'])->where('id_cuti', $id)->first();
        $data = JadwalSatpam::with('user')->where('nip', $nopeg)->whereHasMorph(
            'tagable',
            [Cuti::class],
            function ($query) use ($cuti) {
                $query->where('tagable_id', $cuti->id_cuti);
            }
        )->get();
        return response()->json(JadwalSatpamCalendarResource::collection($data));
    }

    public function datacuti_pengganti(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'nip' => 'required',
        ]);
        $data = JadwalSatpam::with('user')->where('id', $request->id_jadwal)->whereHasMorph(
            'tagable',
            [Cuti::class],
        )->first();
        // assign pengganti
        $data->update([
            'nip_pengganti' => $request->nip,
        ]);
        return redirect()->back()->with('success', 'Berhasil Menambahkan Pengganti ' . $request->nip);
    }

    public function storecuti(Request $request)
    {
        $qrcode_filenamepeg = 'qr-' . explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_cuti)[0] . '.svg';
        $qrcode_filenameat = 'qr-' . $request->atasan . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_cuti)[0] . '.svg';
        $qrcode_filenameatlang = 'qr-' . $request->atasan_lang . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_cuti)[0] . '.svg';

        if (auth()->user()->role == 'admin_bsdm') {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan . '-' .  $request->name_jab . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan_lang . '-' .  $request->name_jab_lang . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameatlang));
        } else {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
        }

        if (auth()->user()->role == 'admin_bsdm') {
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
                'approval' => 2,
                'qrcode_peg' => $qrcode_filenamepeg,
                'qrcode_kepala' => $qrcode_filenameat,
                'qrcode_pejabat' => $qrcode_filenameatlang,
            ]);
        } else {
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
                'qrcode_peg' => $qrcode_filenamepeg,
            ]);
        }


        return redirect()->route('admin.datacuti')->with('success', 'Add Data Berhasil!');
    }

    public function printcuti($id)
    {

        $data = Cuti::join('unit', 'cuti.unit', '=', 'unit.id')->join('users', 'cuti.nopeg', '=', 'users.nopeg')->join('jenis_cuti', 'cuti.jenis_cuti', '=', 'jenis_cuti.id_jeniscuti')->where('id_cuti', $id)->first();
        $atasan = Jabatan::join('users', 'users.atasan', '=', 'jabatan.id')->where('users.atasan', $data->atasan)->first();
        $atasan_lang = Jabatan::join('users', 'users.atasan_lang', '=', 'jabatan.id')->where('users.atasan_lang', $data->atasan_lang)->first();
        // dd($data);

        $pdf = PDF::loadview('admin.printcuti', compact('data', 'atasan', 'atasan_lang'))->setPaper('potrait');
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
