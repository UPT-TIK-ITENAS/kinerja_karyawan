<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;
use Yajra\DataTables\Facades\DataTables;

class ListKaryawanController extends Controller
{

    public function index()
    {
        $peg = User::SelectRaw('users.*,users.id as iduser,unit.*, jb1.nopeg as peg_jab, jb1.nama as name_jab, jb2.nopeg as peg_jab2, jb2.nama as name_jab2')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan as jb1', 'users.atasan', '=', 'jb1.id')
            ->join('jabatan as jb2', 'users.atasan_lang', '=', 'jb2.id')
            ->where('unit', '!=', '29')
            ->get();
        $unit = Unit::get();
        $jabatan = Jabatan::get();

        return view('admin.karyawan', compact('peg', 'unit', 'jabatan'));
    }
	
	public function list(Request $request){
		$users = User::query()->with(['units', 'atasan', 'atasan_langsung'])
		             ->where('unit', '!=', '29');
		return DataTables::of($users)
			->addIndexColumn()
			->addColumn('action', function ($user) {
				return '
				<div class="flex text-center">
					<a href="#" data-toggle="tooltip"
						class="btn btn btn-success btn-xs align-items-center show-karyawan my-1"
						data-id="'. $user->id .'" title="Show Karyawan">
						<i class="icofont icofont-eye-alt"></i>
                	</a>
                	<a href="#" data-toggle="tooltip"
						class="btn btn btn-success btn-xs align-items-center edit-karyawan my-1"
						data-id="'. $user->id .'" title="Edit Karyawan">
						<i class="icofont icofont-edit-alt"></i>
                	</a>
            	</div>
				';
			})
			->toJson();
	}

    public function show($id)
    {
        $user =  User::with(['units', 'atasan', 'atasan_langsung'])
                     ->where('unit', '!=', '29')
                     ->find($id);
        return response()->json($user);
    }
	
	public function update(Request $request, $id)
	{
		$user = User::find($id);
		$user->update($request->all());
		return response()->json($user);
	}
}
