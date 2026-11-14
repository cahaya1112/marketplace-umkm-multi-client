<?php

namespace App\Modules\Ordering\Models;

use App\Modules\Umkm\Umkm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubPesanan extends Model
{
    protected $table = 'sub_pesanan';

    protected $primaryKey = 'id_sub_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_umkm',
        'status_sub_pesanan',
        'kurir_kode',
        'kurir_layanan',
        'estimasi_pengiriman',
        'biaya_pengiriman',
        'nomor_resi',
        'dibuat_pada',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            Pesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(
            Umkm::class,
            'id_umkm',
            'id_umkm'
        );
    }

    public function detail(): HasMany
    {
        return $this->hasMany(
            DetailPesanan::class,
            'id_sub_pesanan',
            'id_sub_pesanan'
        );
    }
}