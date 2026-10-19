<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'user_id'     => 'required|string|max:50|unique:users,user_id,'.$user->id,
            'email'       => 'nullable|email|max:255|unique:users,email,'.$user->id,
            'department'  => 'nullable|string|max:255',
            'course'      => $user->isStudent() ? ['required', Rule::in(User::COURSES)] : 'nullable',
            'section'     => $user->isStudent() ? ['required', Rule::in(User::SECTIONS)] : 'nullable',
        ]);

        $user->update([
            'name'       => trim($data['name']),
            'user_id'    => trim($data['user_id']),
            'email'      => !empty($data['email']) ? trim($data['email']) : null,
            'course'     => $user->isStudent() ? $data['course'] : null,
            'section'    => $user->isStudent() ? $data['section'] : null,
            'department' => !empty($data['department']) ? trim($data['department']) : null,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:6|confirmed',
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', 'Profile and credentials updated successfully.');
    }
}
