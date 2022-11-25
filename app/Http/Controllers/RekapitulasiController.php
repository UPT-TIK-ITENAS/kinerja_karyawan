<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Cuti;
use App\Models\Attendance;

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
        $data = DB::select('CALL getTotalTelatPerBulan(' . $nopeg . ')');
        $data2 = DB::select('CALL getIzinSakit(' . $nopeg . ')');
        $cuti = DB::Select('SELECT cuti.nopeg, cuti.jenis_cuti , MONTH(cuti.tgl_awal_cuti) AS bulan,  YEAR(cuti.tgl_awal_cuti) AS tahun, SUM(cuti.total_cuti) AS totalcuti
        FROM cuti WHERE cuti.nopeg = ' . $nopeg . ' AND cuti.approval = 2 GROUP BY nopeg,bulan,tahun');
        // dd($cuti);

        return view('admin.detailrekap', compact('data', 'data2', 'cuti'));
    }
}
