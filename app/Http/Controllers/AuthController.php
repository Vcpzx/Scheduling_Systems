<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(array_merge($credentials, ['status' => 'approved']))) {
            $request->session()->regenerate();
            return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('schedule.index'));
        }

        return back()->withErrors([
            'auth' => 'Invalid User ID or Password.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|unique:users,user_id',
            'name'     => 'required|string|max:255',
            'role'     => 'required|in:student,teacher',
            'password' => 'required|min:4',
        ]);

        $user = User::create([
            'user_id'  => $data['user_id'],
            'name'     => $data['name'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('login')->with('success', 'Registration submitted. An administrator must approve your account before you can sign in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}