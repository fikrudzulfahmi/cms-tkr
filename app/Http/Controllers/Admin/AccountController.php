<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * Halaman "Akun Saya": semua user CMS (admin, editor, penulis) mengubah
 * profil dan password-nya sendiri. Tidak bisa mengubah role/status.
 */
class AccountController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Admin/Account/Index', [
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->roleLabel(),
                'avatar' => $user->avatar,
                'position' => $user->position,
                'bio' => $user->bio,
                'last_login_at' => $user->last_login_at?->toIso8601String(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'position' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        $data['email'] = strtolower(trim($data['email']));
        unset($data['avatar']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        ActivityLog::record('account_updated', 'akun', 'Memperbarui profil sendiri', $user);

        return back()->with('success', 'Profil Anda berhasil disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|max:72|confirmed',
        ], [
            'current_password.required' => 'Masukkan password saat ini.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        ActivityLog::record('account_password_changed', 'akun', 'Mengubah password sendiri', $user);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
