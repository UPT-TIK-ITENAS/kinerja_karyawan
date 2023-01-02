<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request){
	    $search = $request->search;
	    if ($search == '') {
		    $data = Unit::get();
	    } else {
		    $data = Unit::where('nama_unit', 'like', '%' . $search . '%')
		                ->orWhere('singkatan_unit', 'like', '%' . $search . '%')
		                ->orWhere('kode_unit', 'like', '%' . $search . '%')
		                ->get();
	    }
	    foreach ($data as $d) {
		    $response[] = array(
			    "id" => $d->id,
			    "text" => "$d->kode_unit | $d->nama_unit ($d->singkatan_unit)",
		    );
	    }
	    return response()->json($response);
    }
	
	public function show($id){
		$data = Unit::find($id);
		return response()->json($data);
	}
}
