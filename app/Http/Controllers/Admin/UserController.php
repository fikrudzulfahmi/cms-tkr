<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * Manajemen akun CMS (khusus administrator).
 * Editor dan penulis tidak boleh menambah/mengubah akun.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount('posts')
            ->orderByRaw("FIELD(role, 'admin', 'editor', 'author')")
            ->orderBy('name');

        if ($request->filled('role') && array_key_exists($request->role, User::ROLE_LABELS)) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $term = '%' . addcslashes((string) $request->q, '%_\\') . '%';
            $query->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate(20)->withQueryString()->through(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'role_label' => $u->roleLabel(),
                'position' => $u->position,
                'bio' => $u->bio,
                'is_active' => (bool) $u->is_active,
                'avatar' => $u->avatar,
                'posts_count' => $u->posts_count,
                'last_login_at' => $u->last_login_at?->toIso8601String(),
                'created_at' => $u->created_at?->toIso8601String(),
            ]),
            'filters' => $request->only(['q', 'role']),
            'roles' => collect(User::ROLE_LABELS)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values(),
            'currentUserId' => $request->user()->id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'position' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|max:72',
            'is_active' => 'boolean',
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        $data['email'] = strtolower(trim($data['email']));
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        ActivityLog::record('user_created', 'akun', 'Membuat akun ' . $user->email . ' (' . $user->roleLabel() . ')', $user);

        return back()->with('success', 'Akun ' . $user->name . ' berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'position' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|max:72',
            'is_active' => 'boolean',
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        $me = $request->user();
        $data['email'] = strtolower(trim($data['email']));
        $data['is_active'] = $request->boolean('is_active');

        // Cegah admin mengunci dirinya sendiri.
        if ($me->is($user)) {
            if ($data['role'] !== 'admin') {
                return back()->with('error', 'Anda tidak bisa menurunkan role akun Anda sendiri.');
            }
            if (! $data['is_active']) {
                return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
            }
        }

        // Jangan sampai admin aktif terakhir hilang.
        if (($user->role === 'admin' && $user->is_active)
            && ($data['role'] !== 'admin' || ! $data['is_active'])
            && $this->activeAdminCount() <= 1) {
            return back()->with('error', 'Minimal harus ada satu administrator aktif.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        ActivityLog::record('user_updated', 'akun', 'Memperbarui akun ' . $user->email, $user);

        return back()->with('success', 'Akun ' . $user->name . ' diperbarui.');
    }

    public function toggle(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }
        if ($user->role === 'admin' && $user->is_active && $this->activeAdminCount() <= 1) {
            return back()->with('error', 'Minimal harus ada satu administrator aktif.');
        }

        $user->update(['is_active' => ! $user->is_active]);
        ActivityLog::record(
            $user->is_active ? 'user_activated' : 'user_deactivated',
            'akun',
            ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . ' akun ' . $user->email,
            $user
        );

        return back()->with('success', 'Akun ' . $user->name . ($user->is_active ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    public function destroy(Request $request, User $user)
    {
        $me = $request->user();

        if ($me->is($user)) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }
        if ($user->role === 'admin' && $user->is_active && $this->activeAdminCount() <= 1) {
            return back()->with('error', 'Minimal harus ada satu administrator aktif.');
        }

        // Berita tidak ikut terhapus: dialihkan ke admin yang menghapus.
        Post::where('user_id', $user->id)->update(['user_id' => $me->id]);

        $email = $user->email;
        $user->delete();

        ActivityLog::record('user_deleted', 'akun', 'Menghapus akun ' . $email);

        return back()->with('success', 'Akun ' . $email . ' dihapus. Beritanya dialihkan ke akun Anda.');
    }

    /** Reset paksa: admin mengganti password tanpa password lama. */
    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8|max:72',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        ActivityLog::record('user_password_reset', 'akun', 'Mereset password ' . $user->email, $user);

        return back()->with('success', 'Password ' . $user->name . ' berhasil direset.');
    }

    private function activeAdminCount(): int
    {
        return User::where('role', 'admin')->where('is_active', true)->count();
    }
}
