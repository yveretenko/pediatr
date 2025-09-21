<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        if (Auth::check())
            return redirect()->route('admin.appointments');

        return view('admin.index.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'regex:/^[A-Za-z0-9_]+$/'],
            'password' => 'required',
        ]);

        $credentials=$request->only('username', 'password');

        $success=false;

        $intended=session()->get('url.intended', route('admin.index'));

        if (Auth::attempt($credentials))
        {
            $request->session()->regenerate();

            $success=true;

            $redirect=session()->pull('url.intended', $intended);
        }
        else
            $redirect=$intended;

        return response()->json([
            'success'     => $success,
            'redirect_to' => $redirect,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.index');
    }
}
