<?php

namespace App\Http\Controllers;

use App\Http\Traits\PenilaianKinerja;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JadwalSatpam;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\User;
use App\Models\Mangkir;
use App\Models\KuesionerKinerja;
use App\Http\Resources\KaryawanCalendarResource;
use App\Models\LiburNasional;
use App\Models\QR;
use App\Models\Unit;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Yajra\DataTables\Contracts\DataTable;

class KaryawanController extends Controller
{
    use PenilaianKinerja;

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
        $periode = KuesionerKinerja::where('status', '1')->first();
        $data = collect(
            DB::select("CALL HitungTotalHariKerja('" . auth()->user()->nopeg . "', '$periode->batas_awal', '$periode->batas_akhir')")
        );
        $data = $data->map(function ($item) {
            $item->total_hari_mangkir = $item->total_hari_mangkir - ($item->cuti ?? 0) - ($item->izin_kerja ?? 0) - ($item->izin_sakit ?? 0);
            return $item;
        });
        return view('karyawan.k_index', compact('data', 'periode'));
    }
    public function index_datapresensi()
    {

        return view('karyawan.k_datapresensi');
    }

    public function listdatapresensi(Request $request)
    {
        if ($request->bulan) {
            $month =  explode('-', $request->bulan);
            $data = Attendance::with(['izin'])
                ->where('nip', auth()->user()->nopeg)
                ->whereNotIn('hari', array('6', '0'))
                ->whereMonth('tanggal', $month[0])
                ->whereYear('tanggal', $month[1])
                ->orderBy('tanggal', 'desc');
        } else {
            $data = Attendance::with(['izin'])
                ->where('nip', auth()->user()->nopeg)
                ->whereNotIn('hari', array('6', '0'))
                ->orderBy('tanggal', 'desc');
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('hari', function ($row) {
                    return config('app.days')[$row->hari];
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
                ->addColumn('note', function ($row) {
                    if ($row->status == 0) {
                        $note = 'Kurang';
                    } else {
                        $note = 'Lengkap';
                    }
                    return $note;
                })
                ->addColumn('action', function ($row) {
                    $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    // dd($workingdays);

                    if ($workingdays > 4) {
                        $btn_show = '';
                    } else {
                        $btn_show = '
                        <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
                    }
                    return $btn_show;
                })
                ->addColumn('print', function ($row) {
                    $jenis_izin = $row->izin;
                    $count = $jenis_izin?->count();
                    $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    $html = '';
                    foreach ($jenis_izin as $key => $value) {
                        if ($jenis_izin[$key]->jenis == 1) {
                            $print =  route('karyawan.print.izin', $jenis_izin[$key]->id_izin);
                            $btn_izin = '<a class="btn btn-success btn-xs izin" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a>';
                            $html .=  $btn_izin;
                        }

                        if ($jenis_izin[$key]->jenis == 2) {
                            $print_dispen =  route('karyawan.print.izin', $jenis_izin[$key]->id_izin);
                            $btn_dispen = '<a class="btn btn-success btn-xs dispen" href="' . $print_dispen . '"><i class="icofont icofont-download-alt"></i></a>';
                            $html .=  $btn_dispen;
                        }

                        if ($jenis_izin[$key]->jenis == 3) {
                            $print_sidik_jari =  route('karyawan.print.izin', $jenis_izin[$key]->id_izin);
                            $btn_sidik_jari = '<a class="btn btn-success btn-xs sidik jari" href="' . $print_sidik_jari . '"><i class="icofont icofont-download-alt"></i></a>';
                            $html .=  $btn_sidik_jari;
                        }
                    }
                    return $html;
                })
                ->addColumn('status', function ($row) {
                    // if ($row->izin->count() > 0) {
                    //     if ($row->approval == 1) {
                    //         $apprv = '<span class="badge badge-success">Disetujui</span>';
                    //     } else {
                    //         $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    //     }
                    //     return $apprv;
                    // } else {
                    //     return $apprv = '';
                    // }
                    $izin = $row->izin;
                    $html = '';
                    foreach ($izin as $key => $value) {
                        if ($izin[$key]->jenis == 1) {
                            $status = $izin[$key]->approval == 1 ? '<span class="badge badge-success">Izin : Disetujui Atasan Langsung</span>' : '<span class="badge badge-warning">Izin : Menunggu Persetujuan</span>';
                            $html .=  $status;
                        }

                        if ($izin[$key]->jenis == 2) {
                            if ($izin[$key]->approval == 1) {
                                $status = '<span class="badge badge-success">Sidik Jari : Disetujui Atasan Langsung</span>';
                                $html .=  $status;
                            } else if ($izin[$key]->approval == 5) {
                                $status =  '<span class="badge badge-success">Sidik Jari : Disetujui BSDM</span>';
                                $html .=  $status;
                            } else {
                                $status = '<span class="badge badge-warning">Sidik Jari : Menunggu Persetujuan</span>';
                                $html .=  $status;
                            }
                        }

                        if ($izin[$key]->jenis == 3) {
                            $status = $izin[$key]->approval == 1 ? '<span class="badge badge-success">Dispensasi : Disetujui Atasan Langsung</span>' : '<span class="badge badge-warning">Dispensasi : Menunggu Persetujuan</span>';
                            $html .=  $status;
                        }
                    }
                    return $html;
                })
                ->addColumn('is_cuti', function ($row) {
                    if ($row->is_cuti == 1) {
                        $note = '<div style="font-family: DejaVu Sans, sans-serif;">???</div>';
                    } else {
                        $note = '';
                    }
                    return $note;
                })
                ->addColumn('is_izin', function ($row) {
                    if ($row->is_izin == 1) {
                        $note = '<div style="font-family: DejaVu Sans, sans-serif;">???</div>';
                    } else {
                        $note = '';
                    }
                    return $note;
                })
                ->rawColumns(['duration', 'kurang_jam', 'latemasuk', 'latesiang', 'action', 'print', 'status','is_izin','is_cuti'])
                ->make(true);
        }
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

        if ($request->jenis == 2 || $request->jenis == 3) {

            $tanggalizin = $request->tanggall . ' ' . $request->jam_izin;
            // dd($tanggalizin);
            Izin::insert([
                'id_attendance' => $request->id,
                'nopeg' => $request->nip,
                'name' => $request->name,
                'unit' => $request->unit,
                'tanggal_izin' => Carbon::parse($tanggalizin)->format('Y-m-d H:i:s'),
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
                'tanggal' => $request->tanggall,
                'jam_awal' => $request->jam_awal,
                'jam_akhir' => $request->jam_akhir,
                'alasan' => $request->alasan,
                'validasi' => 1,
                'approval' => 0,
                'jenis' => $request->jenis,
                'qrcode_peg' => $qrcode_filenamepeg,
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan Izin Berhasil!');
    }

    public function index_datarekapitulasi()
    {
        $periode = KuesionerKinerja::where('status', '1')->get();
        $unit = Unit::find(auth()->user()->unit);
        return view('karyawan.k_datarekapitulasi', compact('periode', 'unit'));
    }

    public function listdatarekapitulasi(Request $request)
    {
        $nopeg = auth()->user()->nopeg;
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $data = DB::select("CALL HitungTotalHariKerja('" . auth()->user()->nopeg . "', '$periode->batas_awal', '$periode->batas_akhir')");

        return DataTables::of($data)
            ->editColumn('total_hari_mangkir', function ($row) {
                return $row->total_hari_mangkir - ($row->cuti ?? 0) - ($row->izin_kerja ?? 0) - ($row->izin_sakit ?? 0);
            })
            ->editColumn('kurang_jam', function ($row) {
                return $row->kurang_jam == 0.0 ? '0 Detik' :  \Carbon\CarbonInterval::seconds(($row->kurang_jam * 3600) / 60)->cascade()->forHumans();
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
            ->editColumn('izin_sakit', function ($row) {
                if ($row->izin_sakit != NULL) {
                    $total = $row->izin_sakit . ' ' . 'Hari';
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
        $nopeg = auth()->user()->nopeg;
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
            return DataTables::of($komponen_penilaian)
                ->addIndexColumn()
                ->editColumn('point', function ($row) {
                    return 0;
                })
                ->toJson();
        } else {
            return response()->json([
                'message' => 'Data tidak ditemukan!'
            ], 200);
        }
    }

    public function index_cuti()
    {
        $cuti = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('nopeg', auth()->user()->nopeg)->get();
        $jeniscuti = JenisCuti::all();
        $history_cuti = DB::select("SELECT jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(cuti.total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari
        FROM jenis_cuti LEFT JOIN cuti ON jenis_cuti.id_jeniscuti = cuti.jenis_cuti
        WHERE DATE_FORMAT(tgl_awal_cuti,'%Y') = '" . Carbon::now()->year . "' AND cuti.nopeg='" . auth()->user()->nopeg . "' AND cuti.approval != 3 AND cuti.approval != 0 GROUP BY cuti.jenis_cuti");

        $data = [
            'jeniscuti' => $jeniscuti,
            'cuti'      => $cuti,
            'history'   => $history_cuti
        ];
        // dd($cuti);

        return view('karyawan.k_index_cuti', compact('data'));
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
                    if (date('Y-m-d', $begin) == $value->tanggal  && date("N", $begin) < 5) {
                        $weekends++;
                    }
                }
                $begin += 86400; // +1 day
            };

            $working_days = $no_days - $weekends;

            return response()->json($working_days);
        }
    }

    public function store_cuti(Request $request)
    {
        $qrcode_filenamepeg = 'qr-karyawan' . base64_encode(auth()->user()->nopeg . '-' . $request->jenis_cuti . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . '-' . auth()->user()->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));

        Cuti::insert([
            'nopeg' => auth()->user()->nopeg,
            'name' =>  auth()->user()->name,
            'unit' => auth()->user()->unit,
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
        return redirect()->back()->with('success', 'Pengajuan Cuti Berhasil');
    }

    public function index_izin()
    {
        $izinkerja = IzinKerja::select('izin_kerja.*', 'jenis_izin.jenis_izin as nama_izin')->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')->where('nopeg', auth()->user()->nopeg)->get();
        $jenisizin = JenisIzin::all();

        $data = [
            'jenisizin' => $jenisizin,
            'izinkerja' => $izinkerja
        ];

        // dd($izinkerja);

        return view('karyawan.k_index_izin', compact('data'));
    }

    public function store_izin(Request $request)
    {
        $this->validate($request, [
            'jenis_izin' => 'required',
            'tgl_awal_izin' => 'required',
            'tgl_akhir_izin' => 'required',
            'total_izin' => 'required',
        ]);

        $qrcode_filenamepeg = 'qr-karyawan' . base64_encode(auth()->user()->nopeg . '-' . explode('|', $request->jenis_izin)[0] . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . '-' . auth()->user()->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenamepeg));

        DB::transaction(function () use ($request, $qrcode_filenamepeg) {
            $izin = IzinKerja::create([
                'nopeg' => auth()->user()->nopeg,
                'name' =>  auth()->user()->name,
                'unit' =>  auth()->user()->unit,
                'jenis_izin' => explode('|', $request->jenis_izin)[0],
                'tgl_awal_izin' => $request->tgl_awal_izin,
                'tgl_akhir_izin' => $request->tgl_akhir_izin,
                'total_izin' => $request->total_izin,
                'tgl_pengajuan' => Carbon::now()->toDateTimeString(),
                'validasi' => 1,
                'qrcode_peg' => $qrcode_filenamepeg
            ]);

            if (auth()->user()->fungsi === 'Satpam') {
                $jadwal = JadwalSatpam::with('tagable')->where('nip', auth()->user()->nopeg)->where('tanggal_awal', '>=', $request->tgl_awal_izin . ' 00:00:00')
                    ->where('tanggal_akhir', '<=', $request->tgl_akhir_izin . ' 23:00:00')->get();
                // update jadwal satpam morph
                foreach ($jadwal as $j) {
                    $j->update([
                        'tagable_id' => $izin->id_izinkerja,
                        'tagable_type' => Izin::class,
                    ]);
                }
            }
        });
        return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');
    }

    public function batal_izin($id)
    {
        $data = IzinKerja::where('id_izinkerja', $id)->get();
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
            return redirect()->back()->with('danger', 'Gagal membatalkan cuti');
        }
    }

    public function showDataCalendarByUser($id)
    {
        $id = auth()->user()->nopeg;
        $cuti = Cuti::where('nopeg', $id)->get();
        foreach ($cuti as $key => $value) {
            $cuti[$key]['type'] = 'cuti';
        }
        $izin = IzinKerja::where('nopeg', $id)->get();
        foreach ($izin as $key => $value) {
            $izin[$key]['type'] = 'izin';
        }
        $data = Attendance::with(['user'])->where('nip', $id)->get();
        foreach ($data as $key => $value) {
            $data[$key]['type'] = 'attendance';
        }
        $libur = LiburNasional::get();
        foreach ($libur as $key => $value) {
            $libur[$key]['type'] = 'libur';
        }
        $combine = $data->merge($cuti);
        $combine = $combine->merge($izin);
        $combine = $combine->merge($libur);
        return response()->json(KaryawanCalendarResource::collection($combine));
    }

    public function showDataCalendar(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        if ($type == 'attendance') {
            $data = Attendance::with(['user'])->findOrFail($id);
        } elseif ($type == 'cuti') {
            $data = Cuti::with(['user'])->findOrFail($id);
        } elseif ($type == 'izin') {
            $data = IzinKerja::with(['user'])->findOrFail($id);
        } else {
            $data = Attendance::with(['user'])->findOrFail($id);
        }
        return response()->json($data);
    }

    public function ajuan_mangkir()
    {
        $mangkir = Mangkir::with(['units'])->where('nopeg', auth()->user()->nopeg)->get();
        $data = [
            'mangkir' => $mangkir,
        ];
        return view('karyawan.k_index_ajuan', compact('data'));
    }

    public function store_ajuan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'alasan' => 'required',
        ]);
        $att =  Attendance::where('nip', auth()->user()->nopeg)->where('tanggal', $request->tanggal)->first();

        if ($att != NULL) {
            return redirect()->back()->with('danger', 'Tanggal yang anda ajukan sudah ada di data presensi!');
        } else {
            Mangkir::insert([
                'nopeg' => auth()->user()->nopeg,
                'nama' =>  auth()->user()->name,
                'unit' => auth()->user()->unit,
                'tanggal' => $request->tanggal,
                'alasan' => $request->alasan,
                'status' => '0',
            ]);
            return redirect()->back()->with('success', 'Pengajuan Berhasil');
        }
    }

    public function editprofile()
    {
        $data = User::where('nopeg', auth()->user()->nopeg)->get();
        return view('karyawan.k_profile', compact('data'));
    }

    public function update_profile(Request $request)
    {
        $data = User::where('nopeg', $request->nopeg)->first();
        if($request->password == NULL){
            User::where('nopeg', $request->nopeg)->update([
                'name' => $request->name,
                'nopeg' => $request->nopeg,
                'npp' => $request->npp,
                'masuk_kerja' => $request->masuk_kerja,
                'tempat' => $request->tempat,
                'tanggal_lahir' => $request->tanggal_lahir,
                'email' => $request->email,
                'nohp' => $request->nohp,
                'telegram_id' => $request->telegram_id,
                'password' => $data->password,
            ]);
        }else{
            User::where('nopeg', $request->nopeg)->update([
                'name' => $request->name,
                'nopeg' => $request->nopeg,
                'npp' => $request->npp,
                'masuk_kerja' => $request->masuk_kerja,
                'tempat' => $request->tempat,
                'tanggal_lahir' => $request->tanggal_lahir,
                'email' => $request->email,
                'nohp' => $request->nohp,
                'telegram_id' => $request->telegram_id,
                'password' => password_hash($request->password, PASSWORD_DEFAULT),
            ]);
            
        }

        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }
}
