<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ListKaryawanController extends Controller
{

    public function index()
    {
        return view('admin.karyawan');
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
	
	public function store(Request $request){
		$request->validate([
			'nopeg' => 'required|min:4|max:5|unique:users,nopeg',
			'name' => 'required|min:5',
			'npp' => 'required|min:5|unique:users,npp',
			'tempat' => 'required',
			'tanggal_lahir' => 'required|date',
			'email' => 'sometimes|nullable|email|unique:users,email',
			'nohp' => 'sometimes|nullable|numeric|min:10',
			'unit' => 'required',
			'jabatan' => 'required',
			'atasan' => 'required',
			'atasan_langsung' => 'required',
			'masuk_kerja' => 'required|date',
		]);
		$data = $request->all();
		$password = Carbon::parse($request->tanggal_lahir)->format('dmY');
		$data['password'] = Hash::make($password);
		$data['status'] = 1;
		$data['role'] = 'karyawan';
		$data['fungsi'] = 'Admin';
		User::create($data);
		
		return response()->json([
			'success' => true,
			'message' => 'Karyawan berhasil ditambahkan!'
		], 200);
	}
	
	public function update(Request $request, $id)
	{
		dd($request->all());
		$user = User::find($id);
		$user->update($request->all());
		return response()->json($user);
	}
}
