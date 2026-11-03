<?php

namespace App\Models;

use App\Modules\Umkm\Umkm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * FR-LOG-01 - Audit log bersifat append-only: hanya boleh INSERT.
 *
 * @property int $id_audit
 * @property int $id_pengguna
 * @property string|null $peran
 * @property int|null $id_umkm
 * @property string $aksi
 * @property string $nama_entitas
 * @property int|null $id_entitas
 * @property array|null $data_sebelum
 * @property array|null $data_sesudah
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon $dibuat_pada
 */
class AuditLog extends Model
{
    // Kolom waktu dibuat diisi otomatis, kolom updated_at tidak ada.
    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';
    protected $primaryKey = 'id_audit';

    protected $fillable = [
        'id_pengguna',
        'peran',
        'id_umkm',
        'aksi',
        'nama_entitas',
        'id_entitas',
        'data_sebelum',
        'data_sesudah',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'data_sebelum' => 'array',
            'data_sesudah' => 'array',
            'dibuat_pada' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new LogicException('Audit log bersifat append-only dan tidak boleh diubah.');
        });

        static::deleting(function (): void {
            throw new LogicException('Audit log bersifat append-only dan tidak boleh dihapus.');
        });
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }
}