<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Attendance;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin_v');
    }

    public function datapresensi()
    {
        return view('admin.datapresensi');
    }

    public function rekapitulasi()
    {

        return view('admin.rekapitulasi');
    }

    public function rekapitulasikaryawan()
    {


        return view('admin.rekapitulasi');
    }

    public function listkaryawan(Request $request)
    {
        $data = DB::table('attendance');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('duration', function ($row) {
                    if($row->jam_pulang == NULL){
                        return $durationwork = '';
                    }else{
                        $time_awalreal =  strtotime($row->jam_masuk);
                        $time_akhirreal = strtotime($row->jam_pulang);
                        $duration = ceil(abs($time_akhirreal-$time_awalreal)-strtotime('01:00:00'));
                        $durationwork = date("H:i:s", $duration);
                        return $durationwork;
                    }
                    
                })
                ->editColumn('hari', function ($row) {
                    return config('app.days')[$row->hari];
                })

                ->addColumn('latemasuk', function ($row) {
                    if(date("H:i:s",strtotime($row->jam_masuk)) <= '08:00:00' ){
                        return '';
                    }else if(date("H:i:s",strtotime($row->jam_masuk)) > '08:00:00' && $row->hari != '6'){
                        $durasitelat = strtotime($row->jam_masuk) - strtotime('08:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    }
                })
                ->addColumn('latesiang', function ($row) {
                    if(date("H:i:s",strtotime($row->jam_siang)) <= '12:45:00'){
                    return '';
                    }else if(date("H:i:s",strtotime($row->jam_siang)) > '13:00:00'){
                        $durasitelat = strtotime($row->jam_siang) - strtotime('13:00:00');
                        $durasi = date("H:i:s", $durasitelat);
                        return $durasi;
                    }
                })
                ->rawColumns(['duration','latemasuk','hari','latesiang'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }

    public function listrekapkaryawan(Request $request)
    {
        $data = Attendance::groupBy('nip');
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('duration', function ($row) {

                    $durasitelatmasuk =1;
                    $durasitelatsiang =1;

                    if(!empty($row->jam_masuk) && !empty($row->jam_siang)){
                        if(date("H:i:s",strtotime($row->jam_masuk)) > '08:00:00' ){
                            $durasitelatmasuk = strtotime($row->jam_masuk) - strtotime('08:00:00');
                            $durasi = date("H:i:s", $durasitelatmasuk);
                        }
                         
                        if(date("H:i:s",strtotime($row->jam_siang)) > '13:00:00' ){
                            $durasitelatsiang = strtotime($row->jam_siang) - strtotime('13:00:00');
                            $durasi = date("H:i:s", $durasitelatsiang);
                        }
                
                        $total = $durasitelatsiang + $durasitelatmasuk;
                        $totaljam = date("H:i:s", $total);
                        
                        return $totaljam;
                    }else{
                        return $totaljam = '';
                    }
                    
                    
                })
                ->rawColumns(['duration'])
                ->make(true);
        }
        return DataTables::queryBuilder($data)->toJson();
    }



}
