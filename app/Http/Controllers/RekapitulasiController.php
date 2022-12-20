<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Cuti;
use App\Models\Attendance;
use App\Models\KuesionerKinerja;
use Barryvdh\Debugbar\Facades\Debugbar;

class RekapitulasiController extends Controller
{
    public function index()
    {
        $peg = User::join('unit', 'unit.id', '=', 'users.unit')->wherenot('unit', '29')->where('status', '1')->get();
        // dd($peg[1]);
        return view('admin.rekapitulasi');
    }

    public function listrekapkaryawan(Request $request)
    {
        $data = User::join('unit', 'unit.id', '=', 'users.unit')->wherenot('unit', '29')->where('status', '1')->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('detail', function ($data) {
                    $actionBtn = "
                        <div class='d-block text-center'>
                            <a href='rekapitulasi/detailrekap/$data->nopeg' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-eye-alt'></i></a>
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
        return view('admin.detailrekap', compact('periode', 'nopeg'));
    }

    public function list_detail_rekap(Request $request, $nopeg)
    {
        $periode = KuesionerKinerja::where('id', $request->periode ?? 2)->where('status', '1')->first();
        $data = DB::select("CALL HitungTotalHariKerja('$nopeg', '$periode->batas_awal', '$periode->batas_akhir')");
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('total_hari_mangkir', function ($row) {
                return $row->total_hari_mangkir - ($row->cuti ?? 0) - ($row->izin_kerja ?? 0);
            })
            ->toJson();
    }
}
