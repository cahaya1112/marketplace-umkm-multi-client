<?php

namespace App\Modules\Ordering\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'id_alamat',
        'kode_pesanan',
        'tanggal_pesanan',
        'nama_penerima_snapshot',
        'nomor_telepon_snapshot',
        'alamat_lengkap_snapshot',
        'id_wilayah_tujuan_snapshot',
        'kode_pos_snapshot',
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_pengguna',
            'id_pengguna'
        );
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(
            AlamatPembeli::class,
            'id_alamat',
            'id_alamat'
        );
    }

    public function subPesanan(): HasMany
    {
        return $this->hasMany(
            SubPesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}