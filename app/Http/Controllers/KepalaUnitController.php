<?php

namespace App\Http\Controllers;

use App\Http\Traits\PenilaianKinerja;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\User;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\KuesionerKinerja;
use App\Models\QR;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KepalaUnitController extends Controller
{

    use PenilaianKinerja;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $total_pegawai = User::select(DB::raw('count(*) as total'))->whereNotNull('fungsi')->where('unit', auth()->user()->unit)->count();
        $pengajuan_cuti = Cuti::where('approval', '0')->where('unit', auth()->user()->unit)->count();
        $cuti = Cuti::where('approval', '>', '1')->where('unit', auth()->user()->unit)->count();
        $pengajuan_izin = IzinKerja::where('approval', '0')->where('unit', auth()->user()->unit)->count();
        $izin = IzinKerja::where('approval', '1')->where('unit', auth()->user()->unit)->count();
        // dd($total_pegawai);
        $data = [
            'total_pegawai' => $total_pegawai,
            'pengajuan_cuti' => $pengajuan_cuti,
            'cuti' => $cuti,
            'pengajuan_izin' => $pengajuan_izin,
            'izin' => $izin
        ];
        return view('kepalaunit.kepalaunit_v', compact('data'));
    }

    public function index_datapresensi()
    {
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan', 'users.atasan', '=', 'jabatan.id')
            ->where('unit', auth()->user()->unit)->get();
        // dd($user);

        // $attendance = Attendance::select('tanggal')->groupby('tanggal')->get();
        $attendance = Attendance::select('tanggal')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sat')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sun')
            ->groupby('tanggal')->get();
        // dd($attendance);
        $bulan = Attendance::select(DB::raw('MONTH(tanggal) as bulan'))->groupby('bulan')->get();

        // $a =  Attendance::whereMonth('tanggal','=','7')->get();
        // dd($a);
        return view('kepalaunit.ku_datapresensi', compact('user', 'attendance', 'bulan'));
    }

    public function listdatapresensi(Request $request)
    {
        $data = Attendance::query()->with(['user', 'izin'])
            ->whereRelation('user', 'status', '=', 1)
            ->whereRelation('user', 'unit', auth()->user()->unit)
            ->where('nip', $request->get('filter1'), '', 'and')
            ->where('tanggal', $request->get('filter2'), '', 'and')
            ->where(DB::raw('MONTH(tanggal)'), $request->get('filter3'), '', 'and')
            ->orderby('tanggal', 'asc')->get();


        $days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('days', function ($row) use ($days) {
                return $days[$row->hari];
            })
            ->addColumn('kurang_jam', function ($row) {
                $tanggal = Carbon::now()->format('Y-m-d');
                $durasi = Carbon::parse("$tanggal $row->durasi");
                $telat_masuk = Carbon::parse("$tanggal $row->telat_masuk");
                $telat_pulang = Carbon::parse("$tanggal $row->telat_pulang");
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
            ->addColumn('note', function ($row) {
                if ($row->status == 0) {
                    $note = 'Kurang';
                } else {
                    $note = 'Lengkap';
                }
                return $note;
            })
            ->rawColumns(['latemasuk', 'kurang_jam', 'days', 'latesiang', 'latesore', 'note'])
            ->toJson();
    }

    public function index_datarekapitulasi()
    {
        // dd(DB::select("exec getTotalTelatPerBulan('" . auth()->user()->nopeg . "')"));
        return view('kepalaunit.ku_datarekapitulasi');
    }

    public function listrekapkaryawan(Request $request)
    {
        $data = User::join('unit', 'unit.id', '=', 'users.unit')->where('unit', auth()->user()->unit)->where('status', '1')->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('detail', function ($data) {
                    $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='detailrekap/$data->nopeg' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-eye-alt'></i></a>
                        </div>";
                    return $actionBtn;
                })
                ->rawColumns(['detail'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function detailrekap($nopeg)
    {
        $periode = KuesionerKinerja::where('status', '1')->get();
        return view('kepalaunit.ku_detailrekapitulasi', compact('periode', 'nopeg'));
    }

    public function listdatarekapitulasi(Request $request, $nopeg)
    {
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $data = DB::select("CALL HitungTotalHariKerja('$nopeg', '$periode->batas_awal', '$periode->batas_akhir')");

        return DataTables::of($data)
            ->editColumn('total_hari_mangkir', function ($row) {
                return $row->total_hari_mangkir - ($row->cuti ?? 0) - ($row->izin_kerja ?? 0);
            })
            ->editColumn('kurang_jam', function ($row) {
                return \Carbon\CarbonInterval::seconds(($row->kurang_jam * 3600) / 60)->cascade()->forHumans();
            })
            ->editColumn('total_izin', function ($row) {
                if ($row->total_izin != NULL) {
                    $total = $row->total_izin . ' ' . 'Jam';
                } else {
                    $total = ' ';
                }
                return $total;
            })
            ->editColumn('cuti', function ($row) {
                if ($row->cuti != NULL) {
                    $total = $row->cuti . ' ' . 'Hari';
                } else {
                    $total = ' ';
                }
                return $total;
            })
            ->editColumn('izin_kerja', function ($row) {
                if ($row->izin_kerja != NULL) {
                    $total = $row->izin_kerja . ' ' . 'Hari';
                } else {
                    $total = ' ';
                }
                return $total;
            })

            ->addIndexColumn()
            ->toJson();
    }

    public function penilaian_detail(Request $request, $tipe)
    {
        $nopeg = $request->nopeg;
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $skor = $this->penilaian($nopeg, $periode);
        // dd($skor);
        $new_data = [];
        $no = 1;
        foreach ($skor['izin'] as $key => $value) {
            $new_data[$key]['izin'] = $value;
            $new_data[$key]['sakit'] = $skor['sakit'][$key];
            $new_data[$key]['mangkir'] = $skor['mangkir'][$key];
            $new_data[$key]['kurang_jam'] = $skor['kurang_jam'][$key];
            $new_data[$key]['bulan'] = "Bulan ke-" . $no++;
        }

        $new_data = collect($new_data);

        $penilaian_atasan = $this->penilaian_atasan($nopeg, $periode);

        $komponen_penilaian = [
            0 => [
                'komponen_penilaian' => 'Penilaian Atasan',
                'sub_komponen' => 'Nilai Atasan',
                'bobot' => 40,
                'point' => ($penilaian_atasan * 40) / 100 ?? 0,
            ],
            1 => [
                'komponen_penilaian' => 'Kedisiplinan dan Komitmen Waktu Kerja',
                'sub_komponen' => 'Izin',
                'bobot' => 13,
                'point' => $skor['avg']['izin'] ?? 0,
            ],
            2 => [
                'komponen_penilaian' => 'Kedisiplinan dan Komitmen Waktu Kerja',
                'sub_komponen' => 'Sakit',
                'bobot' => 11,
                'point' => $skor['avg']['sakit'] ?? 0,
            ],
            3 => [
                'komponen_penilaian' => 'Kedisiplinan dan Komitmen Waktu Kerja',
                'sub_komponen' => 'Mangkir',
                'bobot' => 21,
                'point' => $skor['avg']['mangkir'] ?? 0,
            ],
            4 => [
                'komponen_penilaian' => 'Kedisiplinan dan Komitmen Waktu Kerja',
                'sub_komponen' => 'Keterlambatan/Pulang Awal',
                'bobot' => 15,
                'point' => $skor['avg']['kurang_jam'] ?? 0,
            ],
        ];

        $total_point = array_sum(array_column($komponen_penilaian, 'point'));

        if ($tipe == 'detail') {
            return DataTables::of($new_data)->addIndexColumn()->toJson();
        } elseif ($tipe == 'total') {
            return DataTables::of($komponen_penilaian)->addIndexColumn()->toJson();
        } else {
            return response()->json([
                'message' => 'Data tidak ditemukan!'
            ], 200);
        }
    }
    public function index_approval(Request $request)
    {
        $unit =  auth()->user()->unit;
        $data = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->join('users', 'cuti.nopeg', '=', 'users.nopeg')
            ->where('users.unit', $unit)
            ->where('users.role', 'karyawan');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $print =  route('kepalaunit.print.cuti', $data->id_cuti);
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAK" data-bs-toggle="modal" data-id="' . $data->id_cuti . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';

                    return $for_html;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui Atasan Langsung</span>';
                    } else if ($row->approval == 2) {
                        $apprv = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                    } else if ($row->approval == 3) {
                        $apprv = '<span class="badge badge-danger">Ditolak</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['status', 'action'])
                ->toJson();
        }
        return view('kepalaunit.ku_index_approval', compact('data'));
    }

    public function editCuti($id)
    {
        $data = Cuti::where('id_cuti', '=', $id)
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')->first();
        return response()->json($data);
    }

    public function approveCuti(Request $request)
    {

        $qrcode_filenameat = 'qr-atasan' . base64_encode(auth()->user()->nopeg . '-' . auth()->user()->name  .  $request->jenis_cuti . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . '-' . auth()->user()->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));

        Cuti::where('id_cuti', $request->id_cuti)->update([
            'approval' => $request->approval,
            'alasan_tolak' => $request->alasan_tolak,
            'qrcode_kepala' => $qrcode_filenameat,
        ]);

        return redirect()->back()->with('success', 'Pengajuan Cuti disetujui');
    }

    public function index_approvalIzin(Request $request)
    {
        $unit =  auth()->user()->unit;
        $data = IzinKerja::select('izin_kerja.*', 'jenis_izin.jenis_izin as izin')->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')
            ->join('users', 'izin_kerja.nopeg', 'users.nopeg')
            ->join('jabatan', 'users.atasan', '=', 'jabatan.id')
            ->where('users.unit', $unit)->get();
        // dd($data);
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $print =  route('kepalaunit.print.izinkerja', $data->id_izinkerja);
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAK" data-bs-toggle="modal" data-id="' . $data->id_izinkerja . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';

                    return $for_html;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui Atasan Langsung</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['status', 'action'])
                ->toJson();
        }

        // dd(auth()->user()->name);
        return view('kepalaunit.ku_index_approval_izin', compact('data'));
    }

    public function editIzin($id)
    {
        $data = IzinKerja::where('id_izinkerja', $id)
            ->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')->first();
        return response()->json($data);
    }

    public function approveIzin(Request $request)
    {
        $qrcode_filenameat = 'qr-atasan' . base64_encode(auth()->user()->nopeg . '-' . $request->nopeg .  $request->id_izin . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . '-' . auth()->user()->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));

        IzinKerja::where('id_izinkerja', $request->id_izinkerja)->update([
            'approval' => '1',
            'qrcode_kepala' => $qrcode_filenameat,
        ]);

        return redirect()->back()->with('success', 'Izin Berhasil Disetujui');
    }

    public function index_approvalIzinTelat(Request $request)
    {
        $data = Izin::where('unit', auth()->user()->unit)->get();
        // dd($data);
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $print =  route('kepalaunit.print.izin', $data->id_attendance);
                    $for_html = '
                    <a href="#" class="btn btn-warning btn-xs tambahIzin" data-bs-toggle="modal" data-id="' . $data->id_izin . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';

                    return $for_html;
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

        return view('kepalaunit.ku_index_approval_izin_telat', compact('data'));
    }

    public function editIzinTelat($id)
    {
        $data = Izin::where('id_izin', $id)->first();
        // dd($data);
        return response()->json($data);
    }

    public function approveIzinTelat(Request $request)
    {
        $qrcode_filenameat = 'qr-atasan' . $request->nopeg . '-' . auth()->user()->nopeg . '-' . $request->id_attendance . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->name . '-' . auth()->user()->nopeg . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameat));

        $izin = Izin::where('id_attendance', $request->id_attendance)->first();
        if ($request->jenis == '3') {

            $att = Attendance::where('nip', $izin->nopeg)->where('tanggal', Carbon::parse($request->jam)->format('Y-m-d'))->get();
            DB::beginTransaction();

            Izin::where('id_izin', $request->id_izin)->update([
                'approval' => '1',
                'qrcode_kepala' => $qrcode_filenameat,
            ]);

            foreach ($att as $key => $value) {
                if ($value->hari == '5') {
                    $jamsiang1 = Carbon::parse($request->jam)->format('Y-m-d 13:15:00');
                    $value->update([
                        'is_dispen' => 1,
                        'jam_masuk' => Carbon::parse($value->jam_masuk)->format('Y-m-d H:i:s'),
                        'jam_siang' => $jamsiang1,
                        'jam_pulang' => Carbon::parse($value->jam_pulang)->format('Y-m-d H:i:s'),
                        'durasi' => getDurasi($value->jam_masuk, $jamsiang1, $value->jam_pulang),
                        'telat_masuk' => lateMasuk($value->jam_masuk, $jamsiang1, $value->hari),
                        'telat_siang' => lateSiang2($jamsiang1, $value->jam_pulang, $value->hari),
                        'modify_by' => '1',
                    ]);
                } else {
                    $jamsiang2 = Carbon::parse($request->jam)->format('Y-m-d 12:45:00');
                    $value->update([
                        'is_dispen' => 1,
                        'jam_masuk' => Carbon::parse($value->jam_masuk)->format('Y-m-d H:i:s'),
                        'jam_siang' => $jamsiang2,
                        'jam_pulang' => Carbon::parse($value->jam_pulang)->format('Y-m-d H:i:s'),
                        'durasi' => getDurasi($value->jam_masuk, $jamsiang2, $value->jam_pulang),
                        'telat_masuk' => lateMasuk($value->jam_masuk, $jamsiang2, $value->hari),
                        'telat_siang' => lateSiang2($jamsiang2, $value->jam_pulang, $value->hari),
                        'modify_by' => '1',
                    ]);
                }
            }
            $att2 = Attendance::where('nip', $izin->nopeg)->where('tanggal', Carbon::parse($request->jam)->format('Y-m-d'))->get();
            foreach ($att2 as $key => $value) {
                if ($value->jam_masuk == NULL || $value->jam_pulang == NULL) {
                    $value->update([
                        'status' => '0',
                    ]);
                } else {
                    $value->update([
                        'status' => '1',
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Izin Disetujui');
        } else {
            Izin::where('id_izin', $request->id_izin)->update([
                'approval' => '1',
                'qrcode_kepala' => $qrcode_filenameat,
            ]);
            return redirect()->back()->with('success', 'Izin Disetujui');
        }
    }
}
