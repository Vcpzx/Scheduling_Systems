<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate(['name' => 'required|string|max:255', 'user_id' => 'required|string|max:255|unique:users,user_id,'.$user->id]);
        $user->update($data);

        if ($request->filled('password')) {
            $request->validate(['password' => 'sometimes|nullable|min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
