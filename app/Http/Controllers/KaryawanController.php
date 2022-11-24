<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JadwalSatpam;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\QR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KaryawanController extends Controller
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
        $durasi_telat = strtotime('00:00:00');
        $durasi_kerja = strtotime('00:00:00');
        $data_att     = Attendance::where('nip', auth()->user()->nopeg)->whereMonth('tanggal', '=', date('m'))->get();
        foreach ($data_att as $row) {
            if (date("H:i:s", strtotime($row->jam_masuk)) > auth()->user()->awal_tugas && $row->hari != '6' && $row->hari != 0) {
                $durasitelat = strtotime($row->jam_masuk) - strtotime(auth()->user()->awal_tugas);
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
            $durasi_kerja += strtotime($row->jam_keluar) - strtotime($row->jam_pulang);
        }

        $data = [
            'terlambat' =>  date("H:i:s", $durasi_telat),
            'durasi_kerja' => date("H:i:s", $durasi_kerja),
        ];
        return view('karyawan.k_index', compact('data'));
    }
    public function index_datapresensi()
    {
        return view('karyawan.k_datapresensi');
    }

    public function listdatapresensi(Request $request)
    {
        if ($request->bulan) {
            $month =  explode('-', $request->bulan);
            $data = Attendance::selectRaw('attendance.*, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->where('users.nopeg', auth()->user()->nopeg)->whereNotIn('hari', array('6', '0'))->whereMonth('attendance.tanggal', $month[0])->whereYear('attendance.tanggal', $month[1])->orderBy('attendance.tanggal', 'desc');
        } else {
            $data = Attendance::selectRaw('attendance.*, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->where('users.nopeg', auth()->user()->nopeg)->whereNotIn('hari', array('6', '0'))->orderBy('attendance.tanggal', 'desc');
        }
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
                    $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));
                    if ($workingdays < 3) {
                        $addsurat = route('karyawan.createizinkehadiran', $row->id);
                        return $actionBtn = "
                        <div class='d-block text-center'>
                        <a href='$addsurat' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-ui-add'></i></a>
                        </div>
                        ";
                    } else {
                        return '';
                    }
                })
                ->addColumn('file', function ($row) {
                    $dataizin = Attendance::join('izin', 'izin.id_attendance', '=', 'attendance.id')->where('attendance.id', $row->id)->get();

                    foreach ($dataizin as $izin) {
                        $printsurat =  route('karyawan.printizin', $izin->id);

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
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang', 'action', 'file', 'status'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }


    public function index_datarekapitulasi()
    {
        // dd(DB::select("exec getTotalTelatPerBulan('" . auth()->user()->nopeg . "')"));
        return view('karyawan.k_datarekapitulasi');
    }

    public function listdatarekapitulasi(Request $request)
    {
        $data = DB::select('CALL getTotalTelatPerBulan(' . auth()->user()->nopeg . ')');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bulan', function ($row) {
                    return getNamaBulan($row->bulan);
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun;
                })
                ->addColumn('total_telat_pagi', function ($row) {
                    return date('H:i:s', strtotime($row->total_telat_pagi));
                })
                ->addColumn('total_telat_siang', function ($row) {
                    return date('H:i:s', strtotime($row->total_telat_siang));
                })
                ->addColumn('total_telat', function ($row) {
                    return date('H:i:s', strtotime($row->total_telat_siang) + strtotime($row->total_telat_pagi));
                })
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function index_cuti()
    {
        $cuti = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('nopeg', auth()->user()->nopeg)->get();
        $jeniscuti = JenisCuti::all();
        $history_cuti = DB::select("SELECT jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(cuti.total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari 
        FROM jenis_cuti LEFT JOIN cuti ON jenis_cuti.id_jeniscuti = cuti.jenis_cuti 
        WHERE cuti.nopeg='" . auth()->user()->nopeg . "' AND cuti.approval != 3 AND cuti.approval != 0 GROUP BY cuti.jenis_cuti");

        $data = [
            'jeniscuti' => $jeniscuti,
            'cuti'      => $cuti,
            'history'   => $history_cuti
        ];
        // dd($data);

        return view('karyawan.k_index_cuti', compact('data'));
    }

    public function store_cuti(Request $request)
    {
        $is_valid = 0;
        $this->validate($request, [
            'jenis_cuti' => 'required',
            'request->' => 'required',
            'tgl_akhir_cuti' => 'required',
            'total_cuti' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);
        $a = explode('|', $request->jenis_cuti);
        // dd($a);

        $history_cuti = DB::table('jenis_cuti')
            ->select(DB::raw("jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari"))
            ->join('cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('cuti.nopeg', auth()->user()->nopeg)
            ->groupBy('cuti.jenis_cuti')
            ->get();


        dd($history_cuti);
        foreach ($history_cuti as $r) {
            if ($r->id_cuti == $a[0]) {
                if ($r->total_harinya == $r->max_hari) {
                    $is_valid = 1;
                } else if (($r->total_harinya + $request->total_cuti) > $r->max_hari) {
                    $is_valid = 1;
                } else {
                    $is_valid = 0;
                }
            } else if ($r->id_cuti != $a[0]) {
                $is_valid = 0;
            }
        }

        if ($is_valid == 0) {
            $data = new Cuti();
            $data->nopeg = auth()->user()->nopeg;
            $data->unit = auth()->user()->unit;
            $data->name = auth()->user()->name;
            $data->jenis_cuti = $request->jenis_cuti;
            $data->tgl_awal_cuti = $request->tgl_awal_cuti;
            $data->tgl_akhir_cuti = $request->tgl_akhir_cuti;
            $data->total_cuti = $request->total_cuti;
            $data->alamat = $request->alamat;
            $data->no_hp = '0' . str_replace('-', '', $request->no_hp);
            $data->validasi = 1;
            $data->tgl_pengajuan = date('Y-m-d H:i:s');
            $data->save();
            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');
        }
        return redirect()->back()->with('danger', 'Saldo Cuti Tidak Mencukupi');
    }

    public function index_izin()
    {
        $izinkerja = IzinKerja::select('izin_kerja.*', 'jenis_izin.jenis_izin as nama_izin')->join('jenis_izin', 'jenis_izin.id_izin', '=', 'izin_kerja.jenis_izin')->where('nopeg', auth()->user()->nopeg)->get();
        $jenisizin = JenisIzin::all();

        $data = [
            'jenisizin' => $jenisizin,
            'izinkerja' => $izinkerja
        ];

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

        DB::transaction(function () use ($request) {
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
        $delete = IzinKerja::where('id_izinkerja', $id)->delete();
        if ($delete) {
            return redirect()->back()->with('success', 'Berhasil membatalkan izin');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan izin');
        }
    }
    public function batal_cuti($id)
    {
        $delete = Cuti::where('id_cuti', $id)->delete();
        if ($delete) {
            return redirect()->back()->with('success', 'Berhasil membatalkan izin');
        } else {
            return redirect()->back()->with('danger', 'Gagal membatalkan izin');
        }
    }

    public function createizinkehadiran($id)
    {

        $data = Attendance::selectRaw('attendance.*, users.name, users.unit, users.awal_tugas, users.akhir_tugas, unit.nama_unit')
            ->join('users', 'attendance.nip', '=', 'users.nopeg')
            ->join('unit', 'unit.id', '=', 'users.unit')
            ->where('attendance.id', $id)->first();
        return view('karyawan.createizinkehadiran', compact('data'));
    }

    public function storeizinkehadiran(Request $request)
    {

        if ($request->validasi == NULL) {
            return redirect()->route('karyawan.createizinkehadiran', $request->id_izin)->with('error', 'Validasi Tidak diisi!');
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

            return redirect()->route('karyawan.datapresensi')->with('success', 'Pengajuan Izin Berhasil!');
        }
    }

    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();
        $dataqr = QR::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.printizin', compact('data', 'dataqr'))->setPaper('A5', 'landscape');
        return $pdf->stream();
    }
}
