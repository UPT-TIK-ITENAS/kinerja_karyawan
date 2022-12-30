<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function atasan(Request $request){
	    $search = $request->search;
	    if ($search == '') {
		    $data = Jabatan::get();
	    } else {
		    $data = Jabatan::where('nama', 'like', '%' . $search . '%')
		                ->orWhere('nopeg', 'like', '%' . $search . '%')
		                ->get();
	    }
	    foreach ($data as $d) {
		    $response[] = array(
			    "id" => $d->id,
			    "text" =>  ($d->nopeg != '0') ? " $d->nopeg | $d->nama" : "$d->nama",
		    );
	    }
	    return response()->json($response);
    }
	
	public function atasan_langsung(Request $request){
		$search = $request->search;
		if ($search == '') {
			$data = Jabatan::get();
		} else {
			$data = Jabatan::where('nama', 'like', '%' . $search . '%')
			               ->orWhere('nopeg', 'like', '%' . $search . '%')
			               ->get();
		}
		foreach ($data as $d) {
			$response[] = array(
				"id" => $d->id,
				"text" =>  ($d->nopeg != '0') ? " $d->nopeg | $d->nama" : "$d->nama",
			);
		}
		return response()->json($response);
	}
}
