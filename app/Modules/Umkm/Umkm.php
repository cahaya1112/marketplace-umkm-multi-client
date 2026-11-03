<?php

namespace App\Modules\Umkm;

use App\Concerns\DicatatAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Umkm extends Model
{
    // Perubahan data UMKM (tambah, verifikasi/ubah status, hapus) masuk audit log.
    use DicatatAuditLog;

    protected $table = 'umkm';
    protected $primaryKey = 'id_umkm';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'nama',
        'deskripsi',
        'alamat',
        'id_wilayah_asal',
        'kontak',
        'gambar_logo',
        'status',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_pengguna',
            'id_pengguna'
        );
    }
}