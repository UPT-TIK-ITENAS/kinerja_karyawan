<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\Jabatan;
use App\Models\User;
use App\Models\JenisIzin;
use App\Models\JawabanKinerja;
use App\Models\KuesionerKinerja;
use App\Models\DetailRespondenKinerja;
use App\Models\PertanyaanKinerja;
use App\Models\RespondenKinerja;
use App\Models\QR;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PejabatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $total_pegawai = User::select(DB::raw('count(*) as total'))->whereNotNull('fungsi')->where('unit', auth()->user()->unit)->count();
        $auth = auth()->user()->nopeg;  
        $usrs = Jabatan::select('id')
            ->where('nopeg', '=', $auth)
            ->first();

        $pengajuan_cuti = Cuti::join('users', 'cuti.nopeg', '=', 'users.nopeg')
            ->where('cuti.approval', '1')
            ->where('users.atasan_lang', $usrs['id'])
            ->Where('users.atasan', $usrs['id'])->count();

        $cuti = Cuti::join('users', 'cuti.nopeg', '=', 'users.nopeg')
            ->where('cuti.approval', '2')
            ->where('users.atasan_lang', $usrs['id'])
            ->OrWhere('users.atasan', $usrs['id'])->count();

        $data = [
            'total_pegawai' => $total_pegawai,
            'pengajuan_cuti' => $pengajuan_cuti,
            'cuti' => $cuti,
        ];
        return view('pejabat.k_index',compact('data'));
    }

    public function index_datapresensi()
    {
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->where('status', '1')->get();

        $attendance = Attendance::select('tanggal')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sat')
            ->where(DB::raw('date_format(tanggal,"%a")'), '!=', 'Sun')
            ->groupby('tanggal')->get();

        $bulan = Attendance::select(DB::raw('MONTH(tanggal) as bulan'))->groupby('bulan')->get();
        // get attendance limit 10
        // dd(Attendance::with('user')->limit(10)->get());
        return view('pejabat.k_datapresensi', compact('user', 'attendance', 'bulan'));
    }

    public function listdatapresensi(Request $request)
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
            ->rawColumns(['latemasuk', 'days', 'kurang_jam', 'latesiang', 'latesore', 'action_edit', 'action', 'status', 'note'])
            ->toJson();
    }

    public function index_datarekapitulasi()
    {
        $periode = KuesionerKinerja::where('status', '1')->get();
        return view('pejabat.k_datarekapitulasi', compact('periode'));
    }

    public function listrekapkaryawan(Request $request)
    {
        $data = User::join('unit', 'unit.id', '=', 'users.unit')->where('status', '1')->where('unit', '!=', '29')->get();
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

        return view('kepalaunit.ku_dataizin', compact('data'));
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
            return redirect()->back()->with('danger', 'Gagal membatalkan izin');
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


    public function printizin($id)
    {
        $data = Izin::where('id_attendance', $id)->first();
        $dataqr = QR::where('id_attendance', $id)->first();

        $pdf = PDF::loadview('admin.print.izin', compact('data', 'dataqr'))->setPaper('A5', 'landscape');
        return $pdf->stream();
    }

    public function index_approval(Request $request)
    {
        $auth = auth()->user()->nopeg;
        $usrs = Jabatan::select('id')
            ->where('nopeg', '=', $auth)
            ->first();
        $data = Cuti::select('cuti.*', 'jenis_cuti.jenis_cuti as nama_cuti')
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')
            ->join('users', 'cuti.nopeg', '=', 'users.nopeg')
            ->join('jabatan as jb1', 'jb1.id', '=', 'users.atasan_lang')
            ->join('jabatan as jb2', 'jb2.id', '=', 'users.atasan')
            ->where('cuti.approval', '>=', '1')
            ->where('jb1.nopeg', $auth)
            ->where('users.atasan_lang', $usrs['id'])
            ->orWhere('users.atasan', $usrs['id'])
            ->where('users.role', 'karyawan');

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $print =  route('pejabat.print.cuti', $data->id_cuti);
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
        return view('pejabat.k_index_approval', compact('data'));
    }

    public function editCuti($id)
    {
        $data = Cuti::where('id_cuti', '=', $id)
            ->join('jenis_cuti', 'jenis_cuti.id_jeniscuti', '=', 'cuti.jenis_cuti')->first();
        return response()->json($data);
    }

    public function approveCuti(Request $request)
    {

        $qrcode_filenameatlang = 'qr-pejabat' . base64_encode(auth()->user()->nopeg . '-' . auth()->user()->name .  $request->id_jeniscuti . '-' . date('Y-m-d H:i:s') . ')') . '.svg';
        QrCode::format('svg')->size(100)->generate('Sudah divalidasi oleh ' . auth()->user()->nopeg . '-' .  auth()->user()->name . ' Pada tanggal ' .  date('Y-m-d H:i:s'), public_path("qrcode/" . $qrcode_filenameatlang));

        $cuti = Cuti::where('id_cuti', $request->id_cuti)->first();
        if ($request->approval == 2) {

            $attendance = Attendance::where('nip', $cuti->nopeg)->whereBetween('tanggal', [$cuti->tgl_awal_cuti, $cuti->tgl_akhir_cuti])->get();
            DB::beginTransaction();
            $cuti->update([
                'approval' => $request->approval,
                'qrcode_pejabat' => $qrcode_filenameatlang,
            ]);

            foreach ($attendance as $key => $value) {
                $value->update([
                    'is_cuti' => 1,
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan Cuti Disetujui!');
        } else {
            $cuti->update([
                'approval' => $request->approval,
                'alasan_tolak' => $request->alasan_tolak,
                'qrcode_pejabat' => $qrcode_filenameatlang,
            ]);
            return redirect()->back()->with('success', 'Pengajuan Cuti Ditolak!');
        }
    }
}
