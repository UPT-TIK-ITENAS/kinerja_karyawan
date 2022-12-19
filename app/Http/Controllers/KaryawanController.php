<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JadwalSatpam;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\KuesionerKinerja;
use App\Models\QR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonInterval;

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
        $data_att     = Attendance::selectRaw("SUM(durasi) as total_durasi, SEC_TO_TIME(SUM(
            CASE
                 WHEN durasi = '04:00:00' and IS_WEEKDAY(tanggal) = 1 and
                      IF((select if(count(*) > 0, 1, 0) from libur_nasional where tanggal = tanggal), 1, 0) = 0
                     THEN TIME_TO_SEC(telat_masuk) + TIME_TO_SEC(telat_siang) + TIME_TO_SEC('04:00:00')
                 WHEN durasi = '00:00:00' and IS_WEEKDAY(tanggal) = 1 and
                      IF((select if(count(*) > 0, 1, 0) from libur_nasional where tanggal = tanggal), 1, 0) = 0
                     THEN TIME_TO_SEC(telat_masuk) + TIME_TO_SEC(telat_siang) + TIME_TO_SEC('08:00:00')
                ELSE TIME_TO_SEC(telat_masuk) + TIME_TO_SEC(telat_siang)
            END)) as kurang_jam")->where('nip', auth()->user()->nopeg)->whereMonth('tanggal', '=', date('m'))->whereYear('tanggal', '=', date('Y'))->first();

        $hours = floor($data_att->total_durasi / 3600);
        $minutes = floor(($data_att->total_durasi / 60) % 60);
        $seconds = $data_att->total_durasi % 60;
        $data = [
            'terlambat' =>  $data_att->kurang_jam,
            'durasi_kerja' => $hours . ':' . $minutes . ':' . $seconds,
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
            $data = Attendance::with(['izin'])->selectRaw('attendance.*, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->where('users.nopeg', auth()->user()->nopeg)->whereNotIn('hari', array('6', '0'))->whereMonth('attendance.tanggal', $month[0])->whereYear('attendance.tanggal', $month[1])->orderBy('attendance.tanggal', 'desc');
        } else {
            $data = Attendance::with(['izin'])->selectRaw('attendance.*, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->where('users.nopeg', auth()->user()->nopeg)->whereNotIn('hari', array('6', '0'))->orderBy('attendance.tanggal', 'desc');
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('hari', function ($row) {
                    return config('app.days')[$row->hari];
                })
                ->addColumn('file', function ($row) {
                    $printsurat =  route('karyawan.printizin', $row->id);
                    if ($row->izin != null) {
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
                    $print =  route('admin.printizin', $row->id);
                    $workingdays = getWorkingDays($row->tanggal, date('Y-m-d'));

                    if ($hasIzin == null && $workingdays <= 2) {
                        $for_html = '
                    <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
                    } elseif ($hasIzin != null) {
                        $for_html = '
                        <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $row->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                        <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
                    } else {
                        $for_html = '';
                    }
                    return $for_html;
                })
                ->addColumn('status', function ($row) {
                    if ($row->izin != null) {
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
                ->rawColumns(['duration', 'latemasuk', 'hari', 'latesiang', 'action', 'file', 'status'])
                ->make(true);
        }
    }


    public function index_datarekapitulasi()
    {
        $periode = KuesionerKinerja::where('status', '1')->get();
        return view('karyawan.k_datarekapitulasi', compact('periode'));
    }

    public function listdatarekapitulasi(Request $request)
    {
        $nopeg = auth()->user()->nopeg;
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $data = DB::select("CALL HitungTotalHariKerja('" . auth()->user()->nopeg . "', '$periode->batas_awal', '$periode->batas_akhir')");

        return DataTables::of($data)
            ->editColumn('total_hari_mangkir', function ($row) {
                return $row->total_hari_mangkir - ($row->cuti ?? 0) - ($row->izin_kerja ?? 0);
            })
            ->addIndexColumn()
            ->toJson();
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
        // $this->validate($request, [
        //     'jenis_cuti' => 'required',
        //     'tgl_akhir_cuti' => 'required',
        //     'total_cuti' => 'required',
        //     'alamat' => 'required',
        //     'no_hp' => 'required',
        // ]);
        $a = explode('|', $request->jenis_cuti);
        // dd($a);

        $history_cuti = DB::table('jenis_cuti')
            ->select(DB::raw("jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari"))
            ->join('cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('cuti.nopeg', auth()->user()->nopeg)
            ->groupBy('cuti.jenis_cuti')
            ->get();


        //dd($history_cuti);
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
