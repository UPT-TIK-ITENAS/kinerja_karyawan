<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kuesioner;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\Jabatan;
use App\Models\JenisIzin;
use App\Models\JawabanKinerja;
use App\Models\KuesionerKinerja;
use App\Models\DetailRespondenKinerja;
use App\Models\PertanyaanKinerja;
use App\Models\RespondenKinerja;
use App\Models\User;
use App\Models\Unit;
use App\Models\QR;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexKuesioner()
    {
        //$kuesioner = KuesionerKinerja::get();

        //$kuesioner = DB::table('kuisioner_periode')->get();
        $kuesioner = KuesionerKinerja::where('status', '1')->get();
        //dd($kuesioner);
        return view('kuesioner.index_kuesioner', compact('kuesioner'));
    }

    public function showKuesioner($id)
    {
        $auth = auth()->user()->unit;

        $jabatan = Jabatan::select('jabatan.*', 'jabatan.jabatan as j')
            ->join('users', 'jabatan.nopeg', '=', 'users.nopeg')
            ->where('users.unit', auth()->user()->unit)
            ->get();

        $responden = RespondenKinerja::select('nopeg')->get();
        $kuesioner = KuesionerKinerja::with(['pertanyaan' => function ($query) {
            return $query->orderBy('nomor', 'asc');
        }, 'pertanyaan.jawaban'])->find($id);

        $user = User::select('*')
            ->join('unit', 'unit.id', '=', 'users.unit')
            ->where('role', '=', 'karyawan')
            ->where('users.unit', auth()->user()->unit)
            ->where('users.unit', auth()->user()->unit)
            ->whereNotExists(function ($query)  use ($kuesioner) {
                $query->select(DB::raw('nopeg'))
                    ->from('responden_kuisioner')
                    ->whereRaw('users.nopeg = responden_kuisioner.nopeg')
                    ->where('responden_kuisioner.kuisioner_kinerja_id', $kuesioner->id);
            })->get();

        $data = [
            'User' => $user,
            // 'Unit' => $unit,
            'Jabatan' => $jabatan
        ];

        return view('kuesioner.showKuesioner', compact('kuesioner', 'data', 'responden'));
    }

    public function storeKuesioner(Request $request, $id)
    {

        // $request->validate([
        //     'responden.*.jawaban_kinerja_id' => 'required',
        //     'responden.*.pertanyaan_kinerja_id' => 'required',
        //     'nopeg' => 'required',
        //     'nama_pegawai' => 'required',
        // ]);

        $kuesioner = KuesionerKinerja::find($id);

        DB::transaction(function () use ($kuesioner, $request) {
            $total = 0;
            foreach ($request->responden as $key => $value) {
                $total += DB::table('jawaban_kinerja')->where('id', $value['jawaban_kinerja_id'])->value('nilai');
            }
            $indeks = round((($total / 21) * 100), 2);
            $kuesionerResponden = RespondenKinerja::create([
                'kuisioner_kinerja_id' => $kuesioner->id,

                'nopeg_penilai' => explode('-', $request->nama_penilai)[0],
                'nama_penilai' =>  explode('-', $request->nama_penilai)[1],
                'jabatan_penilai' =>  explode('-', $request->nama_penilai)[2],

                'nopeg' => explode('-', $request->nama_pegawai)[0],
                'nama_pegawai' =>  explode('-', $request->nama_pegawai)[1],
                'unit' =>  explode('-', $request->nama_pegawai)[2],
                'jabatan' =>  explode('-', $request->nama_pegawai)[3],

                // 'nama_pegawai' =>  $request->nama_pegawai,
                // 'nopeg' => auth()->user()->nopeg,
                // 'unit' => $request->unit,
                // 'jabatan' => $request->jabatan,

                // 'nopeg_penilai' => $request->nopeg_penilai,
                // 'nama_penilai' => $request->nama_penilai,
                // 'jabatan_penilai' => $request->jabatan_penilai,

                'indeks' => $indeks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // insert detail_respon_akademik
            foreach ($request->responden as $key => $value) {
                DB::table('detail_respon_kinerja')->insert([
                    'responden_kinerja_id' => $kuesionerResponden->id,
                    'pertanyaan_kinerja_id' => $value['pertanyaan_kinerja_id'],
                    'jawaban_kinerja_id' => $value['jawaban_kinerja_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        return redirect()->back()->with('success', 'Berhasil mengirimkan kuesioner!');
    }

    public function pertanyaanPeriode()
    {
        $kuesioner = KuesionerKinerja::get();
        return view('kuesioner.index_periode', compact('kuesioner'));
    }

    public function editPeriode($id)
    {
        $kue = KuesionerKinerja::where('id', $id)->first();
        return response()->json($kue);
    }

    public function updatePeriode(Request $request)
    {
        KuesionerKinerja::where('id', $request->id)->update([
            'judul' => $request->judul,
            'semester' => $request->semester,
            'batas_awal' => $request->batas_awal,
            'batas_akhir' => $request->batas_akhir,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Edit Data Berhasil!');
    }

    public function createPeriode(Request $request)
    {
        KuesionerKinerja::create([
            'judul' => $request->judull,
            'semester' => $request->semesterr,
            'batas_awal' => $request->batas_awall,
            'batas_akhir' => $request->batas_akhirr,
            'status' => $request->statuss
        ]);
        return redirect()->back()->with('success', 'Add Data Berhasil!');
    }

    public function destroyPeriode($id)
    {
        KuesionerKinerja::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }


    public function index_penilaian()
    {
        $periode = KuesionerKinerja::get();
        $data = RespondenKinerja::where('unit', auth()->user()->unit)->get();
        // dd($data);
        return view('kuesioner.hasil_penilaian', compact('periode'));
    }

    public function listPenilaian(Request $request)
    {
        $data =  RespondenKinerja::where('unit', auth()->user()->unit)->where('kuisioner_kinerja_id', $request->get('filter1'), '', 'and')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->toJson();
    }

    public function admHasilKuesioner()
    {
        $periode = KuesionerKinerja::get();
        return view('kuesioner.admhasil_penilaian', compact('periode'));
    }

    public function admlistPenilaian(Request $request)
    {
        $data =  RespondenKinerja::where('kuisioner_kinerja_id', $request->get('filter1'), '', 'and')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->toJson();
    }

    public function pertanyaan()
    {
        $data = PertanyaanKinerja::get();
        $periode = KuesionerKinerja::get();
        return view('kuesioner.pertanyaan', compact('data', 'periode'));
    }

    public function editPertanyaan($id)
    {
        $kue = PertanyaanKinerja::where('id', $id)->first();
        return response()->json($kue);
    }

    public function updatePertanyaan(Request $request)
    {
        PertanyaanKinerja::where('id', $request->id)->update([
            'pertanyaan' => $request->pertanyaan,
            'kuesioner_kinerja_id' => $request->kuesioner_kinerja_id,
        ]);
        return redirect()->back()->with('success', 'Edit Data berhasil!');
    }

    public function jawaban($id)
    {
        $data = JawabanKinerja::where('pertanyaan_kinerja_id', $id)->get();
        return view('kuesioner.jawaban', compact('data'));
    }

    public function editJawaban($id)
    {
        $kue = JawabanKinerja::where('id', $id)->first();
        return response()->json($kue);
    }

    public function updateJawaban(Request $request)
    {
        JawabanKinerja::where('id', $request->id)->update([
            'jawaban' => $request->jawaban,
            'nilai' => $request->nilai,
        ]);

        return redirect()->back()->with('success', 'Edit Data Berhasil!');
    }
}
