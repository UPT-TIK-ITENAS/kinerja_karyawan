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

        return view('kepalaunit.kepalaunit_v');
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
            ->join('users', 'users.nopeg', '=', 'attendance.nip')
            ->whereRelation('user', 'status', '=', 1)
            ->where('nip', $request->get('filter1'), '', 'and')
            ->where('tanggal', $request->get('filter2'), '', 'and')
            ->where(DB::raw('MONTH(tanggal)'), $request->get('filter3'), '', 'and')
            ->where('unit', auth()->user()->unit)
            ->orderby('tanggal', 'asc')->get();


        $days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('days', function ($row) use ($days) {
                return $days[$row->hari];
            })

            ->addColumn('latemasuk', function ($row) {
                $masuk = Carbon::parse($row->jam_masuk)->format('H:i:s');
                $keluar = Carbon::parse('08:00:00')->format('H:i:s');
                if ($row->hari != '6' && $row->hari != '0') {
                    if ($row->jam_masuk == NULL &&  $row->jam_siang != NULL) {
                        $durasi = strtotime(Carbon::parse($row->jam_siang)->format('H:i:s')) - strtotime($keluar);
                        $total = Carbon::parse($durasi)->format('H:i:s');
                    } else {
                        if ($masuk > $keluar) {
                            $durasi = strtotime($masuk) - strtotime($keluar);
                            $total = Carbon::parse($durasi)->format('H:i:s');
                        } else {
                            $total = '';
                        }
                    }
                } else {
                    $total = '';
                }
                return $total;
            })

            ->addColumn('kurang_jam', function ($row) {

                date_default_timezone_set('UTC');
                $masuk = strtotime($row->telat_masuk);
                $siang = strtotime($row->telat_siang);
                $durasi = strtotime($row->durasi);

                if ($row->hari != "6" && $row->hari != "0") {
                    if ($row->durasi == "04:00:00") {
                        $result = date("H:i:s", $siang + $masuk + $durasi);
                    } elseif ($row->durasi == "00:00:00") {
                        $result = "08:00:00";
                    } else {
                        $result = date("H:i:s", $siang + $masuk);
                    }
                } else {
                    $result = "00:00:00";
                }


                return $result;
            })
            ->addColumn('latesiang', function ($row) {
                $siang = Carbon::parse($row->jam_siang)->format('H:i:s');
                $keluar1 = Carbon::parse('13:00:00')->format('H:i:s');
                $keluar2 = Carbon::parse('13:30:00')->format('H:i:s');

                if ($row->hari == '5') {
                    if ($row->jam_siang == NULL && $row->jam_pulang != NULL) {
                        $durasi = strtotime(Carbon::parse($row->jam_pulang)->format('H:i:s')) - strtotime($keluar2);
                        $total = Carbon::parse($durasi)->format('H:i:s');
                    } else {
                        if ($siang > $keluar2) {
                            $durasi = strtotime($siang) - strtotime($keluar2);
                            $total = Carbon::parse($durasi)->format('H:i:s');
                        } else {
                            $total = '';
                        }
                    }
                } elseif ($row->hari != '6' && $row->hari != '0') {
                    if ($row->jam_siang == NULL && $row->jam_pulang != NULL) {
                        $durasi = strtotime(Carbon::parse($row->jam_pulang)->format('H:i:s')) - strtotime($keluar1);
                        $total = Carbon::parse($durasi)->format('H:i:s');
                    } else {
                        if ($siang > $keluar1) {
                            $durasi = strtotime($siang) - strtotime($keluar1);
                            $total = Carbon::parse($durasi)->format('H:i:s');
                        } else {
                            $total = '';
                        }
                    }
                } else {
                    $total = '';
                }

                return $total;
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
                return $row->kurang_jam . ' ' . 'Menit';
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
        $data = IzinKerja::join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')
            ->join('users', 'izin_kerja.nopeg', 'users.nopeg')
            ->join('jabatan', 'users.atasan', '=', 'jabatan.id')
            ->where('users.unit', $unit);

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

        Izin::where('id_izin', $request->id_izin)->update([
            'approval' => '1',
            'qrcode_kepala' => $qrcode_filenameat,
        ]);

        return redirect()->back()->with('success', 'Izin Disetujui');
    }
}
