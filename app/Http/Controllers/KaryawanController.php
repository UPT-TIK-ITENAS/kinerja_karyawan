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
use App\Models\KuesionerKinerja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Barryvdh\Debugbar\Facades\Debugbar;

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
    public function index(Request $request)
    {
        $periode = KuesionerKinerja::where('status', '1')->first();
        $data = collect(DB::select("CALL HitungTotalHariKerja('". auth()->user()->nopeg ."', '$periode->batas_awal', '$periode->batas_akhir')"))->where('bulan',date('m'))->first();
        
        return view('karyawan.k_index', compact('data'));
    }
    public function index_datapresensi()
    {

        // $data = Attendance::query()->with(['user', 'izin'])->whereRelation('user', 'status', '=', 1)->where('nip', auth()->user()->nopeg)->orderby('tanggal', 'asc');
        // dd($data);
        return view('karyawan.k_datapresensi');
    }

    public function listdatapresensi(Request $request)
    {
     
        $data = Attendance::query()->with(['user', 'izin'])->whereRelation('user', 'status', '=', 1)->where('nip', auth()->user()->nopeg)->orderby('tanggal', 'asc');
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
            ->addColumn('action', function ($row) {
                $hasIzin = $row->izin?->count();
                $print =  route('admin.printizin', $row->id);
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


    public function index_datarekapitulasi()
    {
        $periode = KuesionerKinerja::where('status', '1')->get();
        return view('karyawan.k_datarekapitulasi',compact('periode'));
    }

    public function listdatarekapitulasi(Request $request)
    {
        $month =  explode('-', $request->bulan);
        Debugbar::info($request->periode);
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $data = DB::select("CALL HitungTotalHariKerja('auth()->user()->nopeg', '$periode->batas_awal', '$periode->batas_akhir')");
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->toJson();
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
