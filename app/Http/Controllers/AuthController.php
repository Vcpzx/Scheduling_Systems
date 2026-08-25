<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('user_id', $credentials['user_id'])->first();

        if (!$user) {
            return back()->withErrors([
                'auth' => 'Account not found. Please check your User ID or register.',
            ])->withInput($request->only('user_id'));
        }

        if ($user->status === 'pending') {
            return back()->withErrors([
                'auth' => 'Your account registration is still pending administrator approval.',
            ])->withInput($request->only('user_id'));
        }

        if ($user->status === 'rejected') {
            return back()->withErrors([
                'auth' => 'Your account registration was declined by the administrator.',
            ])->withInput($request->only('user_id'));
        }

        if (Auth::attempt(['user_id' => $credentials['user_id'], 'password' => $credentials['password'], 'status' => 'approved'])) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'auth' => 'Incorrect password entered. Please try again or use Forgot Password.',
        ])->withInput($request->only('user_id'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|string|max:50|unique:users,user_id',
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255|unique:users,email',
            'section'  => 'nullable|string|max:50',
            'role'     => 'required|in:student,teacher',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'user_id'    => trim($data['user_id']),
            'name'       => trim($data['name']),
            'email'      => !empty($data['email']) ? trim($data['email']) : null,
            'section'    => !empty($data['section']) ? trim($data['section']) : null,
            'role'       => $data['role'],
            'status'     => 'pending',
            'password'   => Hash::make($data['password']),
        ]);

        return redirect()->route('login')->with('success', 'Registration submitted successfully! An administrator will review and approve your account shortly.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->input('identifier'));
        $user = User::where('user_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'identifier' => 'No registered account found with that User ID or Email address.',
            ])->withInput();
        }

        // Store target user_id in session for password reset verification step
        session(['reset_user_id' => $user->user_id]);

        return redirect()->route('password.reset')->with('info', "Account verified for {$user->name} ({$user->user_id}). Please enter your new password below.");
    }

    public function showResetPassword()
    {
        $userId = session('reset_user_id');
        return view('auth.reset-password', compact('userId'));
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|exists:users,user_id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('user_id', $data['user_id'])->firstOrFail();
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        session()->forget('reset_user_id');

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now sign in with your new credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out successfully.');
    }

    private function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }
        return redirect()->route('student.dashboard');
    }
}