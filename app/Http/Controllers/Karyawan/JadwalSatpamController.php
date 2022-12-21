<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalSatpamCalendarAllResource;
use App\Http\Resources\JadwalSatpamCalendarResource;
use App\Models\JadwalSatpam;
use App\Models\User;
use Illuminate\Http\Request;

class JadwalSatpamController extends Controller
{
    public function index()
    {
        // jadwal satpam count by shift_awal
        $jadwalSatpamCount = JadwalSatpam::selectRaw('shift_awal, count(*) as count')
            ->where('nip', auth()->user()->nopeg)
            ->groupBy('shift_awal')
            ->get()
            ->keyBy('shift_awal');
        return view('karyawan.jadwal-satpam.index', compact('jadwalSatpamCount'));
    }

    public function showDataCalendarByUser($id)
    {
        $data = JadwalSatpam::with(['user', 'tagable'])->where('nip', $id)->orWhere('nip_pengganti', $id)->get();
        return response()->json(JadwalSatpamCalendarAllResource::collection($data));
    }

    public function showDataById($id)
    {
        $data = JadwalSatpam::with(['user', 'tagable', 'pengganti'])->findOrFail($id);
        return response()->json($data);
    }
}
