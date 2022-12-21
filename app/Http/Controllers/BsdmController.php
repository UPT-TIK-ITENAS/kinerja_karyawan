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
        // get attendance limit 10
        // dd(Attendance::with('user')->limit(10)->get());
        return view('bsdm.bsdm_datapresensi', compact('user', 'attendance'));
    }

    public function bsdm_listkaryawan(Request $request)
    {
        $data = Attendance::query()->with(['user', 'izin'])->whereRelation('user', 'status', '=', 1)->where('nip', $request->get('filter1'), '', 'and')->where('tanggal', $request->get('filter2'), '', 'and')->orderby('tanggal', 'asc');
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

                date_default_timezone_set('UTC');
                $masuk = strtotime($row->telat_masuk);
                $siang = strtotime($row->telat_siang);
                $durasi = strtotime($row->durasi);

                if($row->hari != "6" && $row->hari != "0"){
                    if($row->durasi == "04:00:00"){
                        $result = date("H:i:s", $siang + $masuk + $durasi);
                    }elseif($row->durasi == "00:00:00"){
                        $result = "08:00:00";
                    }else{
                        $result = date("H:i:s", $siang + $masuk);
                    }
                }else{
                    $result = "00:00:00";
                }
                
                
                return $result;
                
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
                $print =  route('admin.printizin', $row->id);
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
            ->rawColumns(['latemasuk', 'days','kurang_jam','jenis', 'action_edit', 'action', 'status', 'note'])
            ->toJson();
    }

}
