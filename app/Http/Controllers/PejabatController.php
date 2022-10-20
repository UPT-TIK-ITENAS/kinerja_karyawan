<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\QR;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\DomPDF\Facade\Pdf;

class PejabatController extends Controller
{
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
        return view('pejabat.k_index', compact('data'));
    }

    public function index_datapresensi()
    {
        return view('pejabat.k_datapresensi');
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
                        $addsurat = route('pejabat.createizinkehadiran', $row->id);
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
                        $printsurat =  route('pejabat.printizin', $izin->id);

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
        return view('pejabat.k_datarekapitulasi');
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
        $cuti = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')->where('nopeg', auth()->user()->nopeg)->get();
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

        return view('pejabat.k_index_cuti', compact('data'));
    }

    public function store_cuti(Request $request)
    {
        $is_valid = 0;
        $this->validate($request, [
            'jenis_cuti' => 'required',
            'tgl_awal_cuti' => 'required',
            'tgl_akhir_cuti' => 'required',
            'total_cuti' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        $a = explode('|', $request->jenis_cuti);

        $history_cuti = DB::select("SELECT jenis_cuti.id_jeniscuti AS id_cuti ,jenis_cuti.jenis_cuti AS jeniscuti,sum(total_cuti) AS total_harinya, jenis_cuti.max_hari as max_hari 
        FROM jenis_cuti LEFT JOIN cuti ON jenis_cuti.id_jeniscuti = cuti.jenis_cuti WHERE cuti.nopeg='" . auth()->user()->nopeg . "' GROUP BY cuti.jenis_cuti");

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

        if ($is_valid == 1) {
            return redirect()->back()->with('error', 'Sudah Melebihi Batas Hari Cuti');
        } else if ($is_valid == 0) {
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

        $data = new IzinKerja();
        $data->nopeg = auth()->user()->nopeg;
        $data->unit = auth()->user()->unit;
        $data->name = auth()->user()->name;
        $data->jenis_izin = explode('|', $request->jenis_izin)[0];
        $data->tgl_awal_izin = $request->tgl_awal_izin;
        $data->tgl_akhir_izin = $request->tgl_akhir_izin;
        $data->total_izin = $request->total_izin;
        $data->validasi = 1;
        $data->tgl_pengajuan = date('Y-m-d H:i:s');
        $data->save();
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
            return redirect()->back()->with('error', 'Gagal membatalkan izin');
        }
    }


    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();
        $dataqr = QR::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.printizin', compact('data', 'dataqr'))->setPaper('A5', 'landscape');
        return $pdf->stream();
    }

    public function index_approval(Request $request)
    {
        //$data = DB::select("SELECT * from cuti inner join unit u on u.id =cuti.unit inner join jenis_cuti jc on jc.id_jeniscuti = cuti.jenis_cuti");
        $data = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->where('unit', auth()->user()->unit)->get();
        $jeniscuti = JenisCuti::all();

        //dd($data);
        Debugbar::info($data);

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('action', function ($row) {
                //     return getAksi($row->id_cuti, 'cuti');
                // })
                ->addColumn('action', function ($data) {
                    $delete_url = route('pejabat.destroyCuti', $data->id_cuti);
                    $edit_dd = "<div class='d-block text-center'>
                    <a data-bs-toggle='modal' class='btn btn-success align-items-center editAK fa fa-check' data-id='$data->id_cuti' data-original-title='Edit' data-bs-target='#ProsesCuti'></a>
                    </div>";


                    return $edit_dd;
                })
                ->addColumn('status', function ($row) {
                    if ($row->approval == 1) {
                        $apprv = '<span class="badge badge-success">Disetujui</span>';
                    } else if ($row->approval == 2) {
                        $apprv = '<span class="badge badge-danger">Ditolak</span>';
                    } else {
                        $apprv = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('pejabat.k_index_approval', compact('data'));
    }

    public function editCuti($id)
    {
        $data = Cuti::where('id_cuti', '=', $id)->first();
        return response()->json($data);
    }

    public function destroyCuti($id)
    {
        Cuti::where('id_cuti', $id)->delete();
        return redirect()->route('pejabat.k_index_approval')->with('success', 'Data berhasil dihapus!');
    }

    public function approveCuti(Request $request)
    {
        Cuti::where('id_cuti', $request->id_cuti)->update([
            'approval' => $request->approval
        ]);

        if ($request->approval == 1) {
            return redirect()->back()->with('success', 'Cuti disetujui');
        } else {
            return redirect()->back()->with('error', 'Cuti ditolak');
        }
    }
}
