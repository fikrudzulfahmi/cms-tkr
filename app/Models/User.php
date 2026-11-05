<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'avatar', 'position', 'bio', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Akun panel admin CMS. author = penulis (hanya berita sendiri, wajib review).
    public const CMS_ROLES = ['admin', 'editor', 'author'];

    public const ROLE_LABELS = [
        'admin' => 'Administrator',
        'editor' => 'Editor',
        'author' => 'Penulis',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isCmsUser(): bool { return in_array($this->role, self::CMS_ROLES, true); }

    public function isAdmin(): bool { return $this->role === 'admin'; }

    /** Admin + editor: boleh menyetujui berita dan melihat semua berita. */
    public function canReview(): bool { return in_array($this->role, ['admin', 'editor'], true); }

    public function isAuthor(): bool { return $this->role === 'author'; }

    public function roleLabel(): string { return self::ROLE_LABELS[$this->role] ?? ucfirst((string) $this->role); }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** Halaman tujuan setelah login. */
    public function homeUrl(): string { return '/admin'; }
}
