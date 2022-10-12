<?php

namespace App\Http\Controllers;

use App\Http\Resources\JadwalSatpamCalendarResource;
use App\Models\JadwalSatpam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JadwalSatpamController extends Controller
{
    public function index()
    {
        // jadwal satpam count by shift_awal
        $jadwalSatpamCount = JadwalSatpam::selectRaw('shift_awal, count(*) as count')
            ->groupBy('shift_awal')
            ->get()
            ->keyBy('shift_awal');
        $datauser = User::where('fungsi', 'satpam')->get();
        return view('admin.jadwal-satpam.index', compact('jadwalSatpamCount', 'datauser'));
    }

    public function list(Request $request)
    {
        $data = User::query()->with('jadwal_satpam')->where('fungsi', 'Satpam');
        return DataTables::eloquent($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $route = route('admin.jadwal-satpam.showByUser', $row->nopeg);
                $button = "
                <div class='d-block text-center'>
                    <a href='$route' class='btn btn btn-success btn-xs align-items-center'><i class='icofont icofont-eye-alt'></i></a>
                </div";
                return $button;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'shift_awal' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
        ]);

        if ($request->shift_awal == 'off') {
            $request->tanggal_akhir = $request->tanggal_awal;
        }
        $tanggal_awal = $request->tanggal_awal;
        $jam_awal = Carbon::parse($tanggal_awal)->toTimeString();
        $tanggal_akhir = $request->tanggal_akhir;
        $jam_akhir = Carbon::parse($tanggal_akhir)->toTimeString();

        JadwalSatpam::create([
            'nip' => $request->nip,
            'shift_awal' => $request->shift_awal,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ]);

        return redirect()->route('admin.jadwal-satpam.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function showByUser($nopeg)
    {
        $data = User::query()->where('nopeg', $nopeg)->first();
        return view('admin.jadwal-satpam.show', compact('data'));
    }

    public function storeByUser(Request $request, $nopeg)
    {
        $request->validate([
            'nip' => 'required',
            'shift_awal' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
        ]);

        if ($request->nip != $nopeg) {
            return redirect()->back()->with('danger', 'NIP tidak sesuai');
        }

        if ($request->shift_awal == 'off') {
            $request->tanggal_akhir = $request->tanggal_awal;
        }
        $tanggal_awal = $request->tanggal_awal;
        $jam_awal = Carbon::parse($tanggal_awal)->toTimeString();
        $tanggal_akhir = $request->tanggal_akhir;
        $jam_akhir = Carbon::parse($tanggal_akhir)->toTimeString();

        JadwalSatpam::create([
            'nip' => $request->nip,
            'shift_awal' => $request->shift_awal,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function allDataCalendar()
    {
        $data = JadwalSatpam::with('user')->get();
        return response()->json(JadwalSatpamCalendarResource::collection($data));
    }

    public function showDataCalendarByUser($id)
    {
        $data = JadwalSatpam::with('user')->where('nip', $id)->get();
        return response()->json(JadwalSatpamCalendarResource::collection($data));
    }

    public function showDataById($id)
    {
        $data = JadwalSatpam::with('user')->findOrFail($id);
        return response()->json($data);
    }

    public function destroy($id)
    {
        $data = JadwalSatpam::findOrFail($id);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
