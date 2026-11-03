<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Umkm\Umkm;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id_pengguna
 * @property string $nama
 * @property string $email
 * @property string $kata_sandi
 * @property string $peran
 * @property string|null $nomor_telepon
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 */
#[Fillable(['nama', 'email', 'kata_sandi', 'peran', 'nomor_telepon'])]
#[Hidden(['kata_sandi', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    // Menyesuaikan dengan nama tabel dan primary key pada ERD.
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kata_sandi' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Alias "name" -> kolom "nama".
     * View bawaan starter kit (sidebar, menu pengguna) masih memanggil
     * auth()->user()->name, sedangkan kolom di database bernama "nama".
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->nama);
    }

    public function initials(): string
    {
        $initials = Str::initials($this->nama, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    // Relasi One-to-One ke Model UMKM
    public function umkm(): HasOne
    {
        return $this->hasOne(
            Umkm::class,
            'id_pengguna',
            'id_pengguna'
        );
    }

    // Helper untuk mengecek peran pengguna
    public function hasRole(string $role): bool
    {
        return $this->peran === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPER_ADMIN');
    }

    public function isAdminUmkm(): bool
    {
        return $this->hasRole('ADMIN_UMKM');
    }

    public function isPembeli(): bool
    {
        return $this->hasRole('PEMBELI');
    }

    /**
     * Kolom password yang dipakai untuk autentikasi.
     */
    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }
}