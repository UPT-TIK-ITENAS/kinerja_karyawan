<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login_v');
    }

    public function login(Request $request)
    {
        $data = User::where('nopeg', $request->nopeg)->first();
        // dd($data);
        $login = $request->validate([
            'nopeg' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($login)) {
            if ($data->role == "admin") {
                $request->session()->regenerate();
                return redirect()->intended('admin')->with('success', 'Berhasil Login');
            } else if ($data->role == "dosen") {
                $request->session()->regenerate();
                return redirect()->intended('dosen')->with('success', 'Berhasil Login');
            } else if ($data->role == "kaprodi") {
                $request->session()->regenerate();
                return redirect()->intended('kaprodi')->with('success', 'Berhasil Login');
            }
        } else {
            return redirect()->back()->with('error', 'Username / Password Salah');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('logout', 'Berhasil Logout');
    }
}
