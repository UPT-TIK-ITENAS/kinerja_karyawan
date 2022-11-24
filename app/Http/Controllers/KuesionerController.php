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
        $kuesioner = KuesionerKinerja::all();
        //dd($kuesioner);
        return view('kuesioner.index_kuesioner', compact('kuesioner'));
    }

    public function showKuesioner($id)
    {
        $auth = auth()->user()->unit;

        // $user = User::where('unit', '=', $auth)->get();

        $user = User::select('*')
            ->join('unit', 'unit.id', '=', 'users.unit')
            ->where('users.unit', auth()->user()->unit)
            ->get();

        //$unit = Unit::where('id', '=', $auth)->get();
        $jabatan = Jabatan::select('jabatan.*', 'jabatan.jabatan as j')
            ->join('users', 'jabatan.nopeg', '=', 'users.nopeg')
            ->where('users.unit', auth()->user()->unit)
            ->get();

        // $jabatan = Jabatan::select('id', '=', $auth)
        //     ->join('user as u', 'u.nopeg', '=', 'j.nopeg')
        //     ->get();
        $data = [
            'User' => $user,
            // 'Unit' => $unit,
            'Jabatan' => $jabatan
        ];
        // dd($user);
        $kuesioner = KuesionerKinerja::with(['pertanyaan' => function ($query) {
            return $query->orderBy('nomor', 'asc');
        }, 'pertanyaan.jawaban'])->find($id);
        return view('kuesioner.showKuesioner', compact('kuesioner', 'data'));
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
            $indeks = round($total / count($request->responden), 2);
            // $penilai = explode('-', $request->nama_penilai);
            // dd(explode('-', $request->nama_penilai)[1]);
            $kuesionerResponden = RespondenKinerja::create([
                'kuisioner_kinerja_id' => $kuesioner->id,

                'nopeg_penilai' => explode('-', $request->nama_penilai)[0],
                'nama_penilai' =>  explode('-', $request->nama_penilai)[1],
                'jabatan_penilai' =>  explode('-', $request->nama_penilai)[2],

                'nopeg' => explode('-', $request->nama_pegawai)[0],
                'nama_pegawai' =>  explode('-', $request->nama_pegawai)[1],
                'unit' =>  explode('-', $request->nama_pegawai)[2],

                // 'nama_pegawai' =>  $request->nama_pegawai,
                // 'nopeg' => auth()->user()->nopeg,
                // 'unit' => $request->unit,
                'jabatan' => $request->jabatan,

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
            'keterangan' => $request->keterangan,
            'semester' => $request->semester,
        ]);

        return redirect()->route('admin.pertanyaanPeriode')->with('success', 'Edit Data Berhasil!');
    }

    public function createPeriode(Request $request)
    {
        KuesionerKinerja::create([
            'judul' => $request->judull,
            'keterangan' => $request->keterangann,
            'semester' => $request->semesterr,
        ]);

        return redirect()->route('admin.pertanyaanPeriode')->with('success', 'Add Data Berhasil!');
    }

    public function destroyPeriode($id)
    {
        KuesionerKinerja::where('id', $id)->delete();
        return redirect()->route('admin.pertanyaanPeriode')->with('success', 'Data berhasil dihapus!');
    }

    public function index_penilaian(Request $request)
    {
        $auth = auth()->user()->unit;
        $data = RespondenKinerja::select('*')
            ->join('kuisioner_periode', 'kuisioner_periode.id', '=', 'responden_kuisioner.kuisioner_kinerja_id')
            ->join('unit', 'unit.nama_unit', '=', 'responden_kuisioner.unit')
            ->where('unit.id', auth()->user()->unit)
            ->get();

        //Debugbar::info($data);

        //$data = RespondenKinerja::all();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()

                // ->addColumn('action', function ($data) {
                //     $edit_dd = "<div class='d-block text-center'>
                //         <a data-bs-toggle='modal' class='btn btn-success align-items-center editAK fa fa-pencil' data-id='$data->id_cuti' data-original-title='Edit' data-bs-target='#ProsesCuti'></a>
                //         </div>";

                //     return $edit_dd;
                // })
                ->addColumn('status', function ($row) {
                    if ($row->indeks <= 1.5) {
                        $apprv = '<span class="badge badge-warning">Kurang puas</span>';
                    } else if ($row->indeks < 2 || $row->indeks <= 2.5) {
                        $apprv = '<span class="badge badge-success">Puas</span>';
                    } else if ($row->indeks >= 2.6) {
                        $apprv = '<span class="badge badge-success">Sangat Puas</span>';
                    }
                    return $apprv;
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        return view('kuesioner.hasil_penilaian', compact('data'));
    }
}
