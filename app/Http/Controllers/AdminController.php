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
use App\Models\AttendanceBaru;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\IzinKerja;
use App\Models\JadwalSatpam;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\LiburNasional;
use App\Models\Jabatan;
use App\Models\Mangkir;
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
        $user_info = User::groupBy('unit')->select('unit', 'singkatan_unit', DB::raw('count(*) as total'))->join('unit', 'users.unit', '=', 'unit.id')->whereNotNull('fungsi')->get();
        $pengajuan_cuti = Cuti::where('approval', '0')->count();
        $cuti = Cuti::where('approval', '2')->count();
        $pengajuan_izin = IzinKerja::where('approval', '0')->count();
        $izin = IzinKerja::where('approval', '1')->count();

        $data = [
            'user_info' => $user_info,
            'pengajuan_cuti' => $pengajuan_cuti,
            'cuti' => $cuti,
            'pengajuan_izin' => $pengajuan_izin,
            'izin' => $izin
        ];
        return view('admin.admin_v', compact('data'));
    }

    //END DASHBOARD

    // Data Izin Per Hari
    public function index_izin_perhari(Request $request)
    {
        $data = Izin::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    if ($data->approval == 1) {
                        $print =  route('kepalaunit.print.izin', $data->id_attendance);
                        $for_html = '
                        <a href="#" class="btn btn-warning btn-xs tambahIzin" data-bs-toggle="modal" data-id="' . $data->id_izin . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                        <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';

                        return $for_html;
                    }
                    return "";
                })
                ->addColumn('waktu', function ($data) {
                    if ($data->tanggal != NULL && $data->jam_awal != NULL && $data->jam_akhir != NULL) {
                        $waktu = $data->tanggal . ' ' . $data->jam_awal . ' s/d ' . $data->jam_akhir;
                    } else {
                        $waktu = $data->tanggal_izin;
                    }

                    return $waktu;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui Atasan Langsung</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->editColumn('jenis', function ($row) {
                    if ($row->jenis == 1) {
                        $stat = 'Izin terlambat/Ada keperluan,dll';
                    } elseif ($row->jenis == 2) {
                        $stat = 'Absen sidik jari tidak terbaca mesin';
                    } else {
                        $stat = 'Dispensasi';
                    }
                    return $stat;
                })
                ->rawColumns(['status', 'action', 'waktu', 'jenis'])
                ->toJson();
        }
        return view('admin.izin-perhari');
    }

    public function edit_izin_perhari($id)
    {
        $data = Izin::where('id_izin', $id)->first();
        $attendance = Attendance::whereDate('tanggal', Carbon::parse($data->tanggal_izin)->format('Y-m-d'))->where('nip', $data->nopeg)->first();
        return response()->json(['izin' => $data, 'attendance' => $attendance]);
    }

    public function update_izin_perhari(Request $request, $id)
    {
        $data = Izin::where('id_izin', $id)->first();
        $attendance = Attendance::whereDate('tanggal', Carbon::parse($data->tanggal_izin)->format('Y-m-d'))->where('nip', $data->nopeg)->first();
        $attendance->update([
            'jam_masuk' => $request->jam_masuk,
            'jam_siang' => $request->jam_siang,
            'jam_pulang' => $request->jam_pulang,
            'telat_masuk' => lateMasuk($request->jam_masuk, $request->jam_siang, $attendance->hari),
            'telat_siang' => lateSiang2($request->jam_siang, $request->jam_pulang, $attendance->hari),
            'durasi' => getDurasi($request->jam_masuk, $request->jam_siang, $request->jam_pulang),
        ]);

        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }

    //DATA PRESENSI
    public function datapresensi()
    {
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->where('status', '1')->get();

        $attendance = Attendance::select('tanggal')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sat')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sun')
            ->groupby('tanggal')->get();

        $bulan = Attendance::select(DB::raw('MONTH(tanggal) as bulan'))->groupby('bulan')->get();
        // get attendance limit 10
        // dd(Attendance::with('user')->limit(10)->get());
        return view('admin.datapresensi', compact('user', 'attendance', 'bulan'));
    }

    public function datapresensi_duration()
    {
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->where('status', '1')->get();

        $attendance = AttendanceBaru::select('tanggal')->groupby('tanggal')->get();
        // get attendance limit 10
        // dd(Attendance::with('user')->limit(10)->get());
        return view('admin.datapresensi-duration', compact('user', 'attendance'));
    }


    public function listkaryawan(Request $request)
    {
        $data = Attendance::query()->with(['user', 'izin'])
            ->whereRelation('user', 'status', '=', 1)
            ->where('nip', $request->get('filter1'), '', 'and')
            ->where('tanggal', $request->get('filter2'), '', 'and')
            ->where(DB::raw('MONTH(tanggal)'), $request->get('filter3'), '', 'and')
            ->orderby('tanggal', 'asc');

        $days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('days', function ($row) use ($days) {
                return $days[$row->hari];
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
                $hasIzin = $row->izin?->count();
                $print =  route('admin.print.izin', $row->id);
                if ($hasIzin == null) {
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
                } else {
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
                }

                return $for_html;
            })
            ->addColumn('action_edit', function ($row) {
                return getAksi($row->id, 'att_edit');
            })
            ->addColumn('kurang_jam', function ($row) {
                $tanggal = Carbon::now()->format('Y-m-d');
                $durasi = Carbon::parse("$tanggal $row->durasi");
                $telat_masuk = Carbon::parse("$tanggal $row->telat_masuk");
                $telat_pulang = Carbon::parse("$tanggal $row->telat_siang");
                if ($durasi->equalTo("$tanggal 08:00:00")) {
                    $base_time = Carbon::parse("$tanggal 00:00:00");
                    $total = $base_time->addHours($telat_masuk->format('H'))->addMinutes($telat_masuk->format('i'))->addSeconds($telat_masuk->format('s'));
                    $total = $total->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'));
                } else if ($durasi->equalTo("$tanggal 04:00:00")) {
                    $base_time = Carbon::parse("$tanggal 04:00:00");
                    $total = $base_time->addHours($telat_masuk->format('H'))->addMinutes($telat_masuk->format('i'))->addSeconds($telat_masuk->format('s'));
                    $total = $total->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'));
                } else {
                    $base_time = Carbon::parse("$tanggal 08:00:00");
                    $total = $durasi->addHours($telat_masuk->format('H'))->addMinutes($telat_masuk->format('i'))->addSeconds($telat_masuk->format('s'));
                    $total = $total->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'));
                }
                return $total->format('H:i:s');
            })
            ->addColumn('status', function ($row) {
                if ($row->izin->count() > 0) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                } else {
                    return $apprv = '';
                }
            })
            ->rawColumns(['latemasuk', 'days', 'kurang_jam', 'latesiang', 'latesore', 'action_edit', 'action', 'status', 'note'])
            ->toJson();
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'tanggal' => 'required',
        ]);

        $currentData = Attendance::where('nip', $request->nip)->where('tanggal', Carbon::parse($request->tanggal)->format('Y-m-d'))->first();
        $hari = date('w', strtotime($request->tanggal));
        date_default_timezone_set('UTC');

        // if currentData is null, then create new data
        if (!$currentData) {
            Attendance::insert([
                'nip' => $request->nip,
                'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
                'hari' => $hari,
                'jam_masuk' => $request->jam_masuk == NULL ? NULL :  Carbon::parse($request->jam_masuk)->format('Y-m-d H:i:s'),
                'jam_siang' => $request->jam_siang == NULL ? NULL :  Carbon::parse($request->jam_siang)->format('Y-m-d H:i:s'),
                'jam_pulang' => $request->jam_pulang == NULL ? NULL :  Carbon::parse($request->jam_pulang)->format('Y-m-d H:i:s'),
                'durasi' => getDurasi($request->jam_masuk, $request->jam_siang, $request->jam_pulang),
                'telat_masuk' => lateMasuk($request->jam_masuk, $request->jam_siang, $hari),
                'telat_siang' => lateSiang2($request->jam_siang, $request->jam_pulang, $hari),
                'status' => $request->status
            ]);
            return redirect()->back()->with('success', 'Data Attendance berhasil disimpan');
        } else {
            return redirect()->back()->with('error', "Data Attendance pada NIP $request->nip Tanggal $request->tanggal sudah ada! Mohon edit attendance.");
        }
    }

    public function updateAttendance(Request $request)
    {
        $attendance = Attendance::where('id', $request->id2)->first();
        // dd(lateSiang2($request->jam_siang1, $request->jam_pulang1, $attendance->hari));
        $attendance->update([
            'jam_masuk' => Carbon::parse($request->jam_masuk1)->format('Y-m-d H:i:s'),
            'jam_siang' => Carbon::parse($request->jam_siang1)->format('Y-m-d H:i:s'),
            'jam_pulang' => Carbon::parse($request->jam_pulang1)->format('Y-m-d H:i:s'),
            'durasi' => getDurasi($request->jam_masuk1, $request->jam_siang1, $request->jam_pulang1),
            'telat_masuk' => lateMasuk($request->jam_masuk1, $request->jam_siang1, $attendance->hari),
            'telat_siang' => lateSiang2($request->jam_siang1, $request->jam_pulang1, $attendance->hari),
            'modify_by' => '1',
            'status' => $request->status1,
        ]);
        return redirect()->back()->with('success', 'Data Attendance berhasil diupdate');
    }

    public function listkaryawan_duration(Request $request)
    {
        $data = AttendanceBaru::query()->with(['user', 'izin'])->whereRelation('user', 'status', '=', 1)->orderby('tanggal', 'asc');
        $days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('days', function ($row) use ($days) {
                return $days[$row->hari];
            })

            ->addColumn('latemasuk', function ($row) {
                return $row->telat_masuk;
            })

            ->addColumn('latesiang', function ($row) {
                return $row->telat_siang;
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
                $hasIzin = $row->izin?->count();
                $print =  route('admin.print.izin', $row->id);
                if ($hasIzin == null) {
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
                } else {
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
                }

                return $for_html;
            })
            ->addColumn('status', function ($row) {
                if ($row->izin != NULL) {
                    if ($row->izin->approval == '1') {
                        $apprv = '<span class="badge badge-success">Disetujui Atasan Langsung</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                } else {
                    return $apprv = '';
                }
            })
            ->rawColumns(['latemasuk', 'days', 'latesiang', 'latesore', 'action', 'status', 'note'])
            ->toJson();
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
        $qrcode_filenamepeg = 'qr-karyawan' . base64_encode($request->nip . '-' . $request->id . '-' . date('Y-m-d H:i:s') . ')') . '.svg';

        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->nip . '-' . $request->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));

        if ($request->jenis == 1) {
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
                'jenis' => $request->jenis,
                'qrcode_peg' => $qrcode_filenamepeg,
            ]);
        } else {
            Izin::insert([
                'id_attendance' => $request->id,
                'nopeg' => $request->nip,
                'name' => $request->name,
                'unit' => $request->unit,
                'tanggal_izin' => date('Y-m-d H:i:s', strtotime($request->tanggal_izin)),
                'alasan' => $request->alasan,
                'validasi' => 1,
                'approval' => 0,
                'jenis' => $request->jenis,
                'qrcode_peg' => $qrcode_filenamepeg,
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan Izin Berhasil!');
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
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->whereNotNull('status')->get();
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
        $data = IzinKerja::with(['units'])->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('print', function ($row) {
                $printizin =  route('admin.print.izinkerja', $row->id_izinkerja);
                $batal_izin = route('admin.izin-resmi.batal_izin', $row->id_izinkerja);
                $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a>
        <a class="btn btn-danger btn-xs batalizin" href="' . $batal_izin . '">X</a>';
                return $for_html;
            })
            ->addColumn('status', function ($row) {
                $batal_izin = route('admin.izin-resmi.batal_izin', $row->id_izinkerja);
                if ($row->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalizin" title="Batal Izin" href="' . $batal_izin . '">X</a>';
                }
                return $for_html;
            })
            ->rawColumns(['print', 'status'])
            ->toJson();
    }

    public function storeizin(Request $request)
    {
        $qrcode_filenamepeg = 'qr-karyawan' . base64_encode(explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_izin)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        $qrcode_filenameat = 'qr-atasan' . base64_encode($request->atasan . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_izin)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';

        if (auth()->user()->role == 'admin_bsdm') {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan . '-' .  $request->name_jab . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));
        } else {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
        }

        if (auth()->user()->role == 'admin_bsdm') {
            $izin = IzinKerja::create([
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
            $attendance = Attendance::where('nip', $izin->nopeg)->whereBetween('tanggal', [$izin->tgl_awal_izin, $izin->tgl_akhir_izin])->get();
            DB::beginTransaction();
            foreach ($attendance as $key => $value) {
                $value->update([
                    'is_izin' => 1,
                ]);
            }
            DB::commit();
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

        return redirect()->back()->with('success', 'Pengajuan Izin Berhasil!');
    }


    public function batal_izin($id)
    {
        $data = IzinKerja::where('id_izinkerja', $id)->first();
        $attendance = Attendance::where('nip', $data->nopeg)->whereBetween('tanggal', [$data->tgl_awal_izin, $data->tgl_akhir_izin])->get();
        DB::beginTransaction();
        foreach ($attendance as $key => $value) {
            $value->update([
                'is_izin' => 0,
            ]);
        }
        $data->delete();
        DB::commit();
        if ($data) {
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
            ->whereNotNull('status')->get();

        // dd($user);
        $jeniscuti = JenisCuti::all();

        $data = [
            'user' => $user,
            'jeniscuti' => $jeniscuti
        ];
        return view('admin.datacuti', compact('data'));
    }

    public function historycuti($nopeg, $jenis)
    {
        // dd(Carbon::now()->year);
        $history_cuti =
            cuti::join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('cuti.nopeg', $nopeg)
            ->where('cuti.jenis_cuti', $jenis)
            ->where(DB::raw("(DATE_FORMAT(tgl_awal_cuti,'%Y'))"), Carbon::now()->year)
            ->GROUPBY('cuti.jenis_cuti')->sum('total_cuti');
        return response()->json($history_cuti);
    }

    public function listcuti(Request $request)
    {
        $data = Cuti::query()->with(['units', 'jeniscuti']);
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $printcuti =  route('admin.print.cuti', $row->id_cuti);
                    $batal_cuti = route('admin.cuti.batal_cuti', $row->id_cuti);
                    return '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a>
            <a class="btn btn-danger btn-xs batalcuti" href="' . $batal_cuti . '">X</a>';
                })
                ->addColumn('status', function ($row) {
                    $batal_cuti = route('admin.cuti.batal_cuti', $row->id_cuti);
                    if ($row->approval == 1) {
                        $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                    } elseif ($row->approval == 2) {
                        $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                    } elseif ($row->approval == 3) {
                        $for_html = '<span class="badge badge-danger">Ditolak</span><br><p><b>note</b> : ' . $row->alasan . '</p>';
                    } else {
                        $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalcuti" title="Batal Cuti" href="' . $batal_cuti . '">X</a>';
                    }
                    return $for_html;
                })
                ->rawColumns(['action', 'status'])
                ->toJson();
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
        $qrcode_filenamepeg = 'qr-karyawan' . base64_encode(explode('-', $request->nopeg)[0] . '-' . explode('|', $request->jenis_cuti)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        $qrcode_filenameat = 'qr-atasan' . base64_encode($request->atasan . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_cuti)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        $qrcode_filenameatlang = 'qr-pejabat' . base64_encode($request->atasan_lang . '-' . explode('-', $request->nopeg)[0] .  explode('|', $request->jenis_cuti)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';

        if (auth()->user()->role == 'admin_bsdm') {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan . '-' .  $request->name_jab . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . $request->atasan_lang . '-' .  $request->name_jab_lang . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameatlang));
        } else {
            QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . explode('-', $request->nopeg)[0] . '-' . explode('-', $request->nopeg)[1] . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));
        }

        if (auth()->user()->role == 'admin_bsdm') {
            $cuti = Cuti::create([
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

            $attendance = Attendance::where('nip', $cuti->nopeg)->whereBetween('tanggal', [$cuti->tgl_awal_cuti, $cuti->tgl_akhir_cuti])->get();
            DB::beginTransaction();
            foreach ($attendance as $key => $value) {
                $value->update([
                    'is_cuti' => 1,
                ]);
            }
            DB::commit();
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
        return redirect()->back()->with('success', 'Berhasil menambah data cuti');
    }


    public function batal_cuti($id)
    {
        $data = Cuti::where('id_cuti', $id)->first();
        $attendance = Attendance::where('nip', $data->nopeg)->whereBetween('tanggal', [$data->tgl_awal_cuti, $data->tgl_akhir_cuti])->get();
        DB::beginTransaction();
        foreach ($attendance as $key => $value) {
            $value->update([
                'is_cuti' => 0,
            ]);
        }
        $data->delete();
        DB::commit();
        if ($data) {
            return redirect()->back()->with('success', 'Berhasil membatalkan cuti');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan cuti');
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
        $data = LiburNasional::orderBy('tanggal', 'asc')->get();
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

        return redirect()->route('admin.libur-nasional.libur')->with('success', 'Edit Data Berhasil!');
    }

    public function createlibur(Request $request)
    {
        LiburNasional::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.libur-nasional.libur')->with('success', 'Add Data Berhasil!');
    }

    public function destroylibur($id)
    {
        LiburNasional::where('id', $id)->delete();
        return redirect()->route('admin.libur-nasional.libur')->with('success', 'Data berhasil dihapus!');
    }

    // Ajuan
    public function index_ajuan()
    {
        $mangkir = Mangkir::get();
        $data = [
            'mangkir' => $mangkir,
        ];
        return view('admin.dataajuan', compact('data'));
    }

    public function detail_mangkir(Request $request, $id)
    {
        $mangkir = Mangkir::with(['units'])->where('id_mangkir', $id)->first();
        return response()->json($mangkir);
    }

    public function update_ajuan(Request $request)
    {
        $request->validate([
            'id_mangkir' => 'required',
            'tanggal' => 'required',
            'alasan' => 'required',
            'nopeg' => 'required',
            'nama' =>  'required',
        ]);

        DB::beginTransaction();

        $mangkir = Mangkir::where('id_mangkir', $request->id_mangkir)->first();
        $mangkir->update([
            'status' => 2
        ]);

        $attendance = Attendance::where('tanggal', $request->tanggal)->where('nip', $request->nopeg)->first();
        if ($attendance) {
            return redirect()->back()->with('error', 'Data Gagal Diperbarui! Data sudah terdapat pada attendance.');
        }

        $day = date("w", strtotime($request->tanggal));
        $jam_masuk = "$request->tanggal 08:00:00";
        if ($day == 5) {
            $jam_siang = "$request->tanggal 13:30:00";
        } else {
            $jam_siang = "$request->tanggal 13:00:00";
        }
        $jam_pulang = "$request->tanggal 17:00:00";

        Attendance::create([
            'nip' => $request->nopeg,
            'hari' => $day,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $jam_masuk,
            'jam_siang' => $jam_siang,
            'jam_pulang' => $jam_pulang,
            'status' => 1,
            'telat_masuk' => lateMasuk($jam_masuk, $jam_siang, $day),
            'telat_siang' => lateSiang2($jam_siang, $jam_pulang, $day),
            'durasi' => getDurasi($jam_masuk, $jam_pulang, $day),
            'modify_by' => 0,
            'is_cuti' => 0,
            'is_izin' => 0,
            'is_dispen' => 0,
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Data Berhasil Diperbarui! Data sudah terdapat pada attendance.');
    }
}
