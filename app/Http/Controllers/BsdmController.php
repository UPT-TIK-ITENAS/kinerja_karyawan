<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Izin;
use App\Models\QR;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BsdmController extends Controller
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

    public function bsdm_datapresensi()
    {
        $user = User::SelectRaw('users.*,unit.*,jabatan.nopeg as peg_jab, jabatan.nama as name_jab')->join('unit', 'users.unit', '=', 'unit.id')->join('jabatan', 'users.atasan', '=', 'jabatan.id')->where('status', '1')->get();

        $attendance = Attendance::select('tanggal')->groupby('tanggal')->get();
        $bulan = Attendance::select(DB::raw('MONTH(tanggal) as bulan'))->groupby('bulan')->get();
        // get attendance limit 10
        // dd(Attendance::with('user')->limit(10)->get());
        return view('bsdm.bsdm_datapresensi', compact('user', 'attendance', 'bulan'));
    }

    public function bsdm_listkaryawan(Request $request)
    {
        $data = Attendance::query()->with(['user', 'izin'])->whereRelation('user', 'status', '=', 1)
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
            ->addColumn('jenis', function ($row) {
                $hasIzin = $row->izin?->count();
                if ($hasIzin == null) {
                    $note = '';
                } else {
                    if ($row->jenis == 1) {
                        $note = 'Izin';
                    } else {
                        $note = 'Sidik Jari';
                    }
                    return $note;
                }

                return $note;
            })
            ->addColumn('action', function ($row) {
                $hasIzin = $row->izin?->count();
                $print =  route('admin.print.izin', $row->id);
                if ($hasIzin == null) {
                    $for_html = '';
                } else {
                    $for_html = '
                    <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
                }

                return $for_html;
            })
            ->addColumn('action_edit', function ($row) {
                return getAksi($row->id, 'att_edit');
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
            ->rawColumns(['latemasuk', 'days', 'kurang_jam', 'jenis', 'action_edit', 'action', 'status', 'note','is_cuti','is_izin'])
            ->toJson();
    }
}
