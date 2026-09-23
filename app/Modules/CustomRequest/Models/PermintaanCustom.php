<?php

namespace App\Modules\CustomRequest\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanCustom extends Model
{
    protected $table = 'permintaan_custom';

    protected $primaryKey = 'id_permintaan_custom';

    public $timestamps = false;

    protected $fillable = [
        'id_item_pesanan',
        'spesifikasi_custom',
        'status_custom',
        'harga_custom',
        'alasan_penolakan',
        'id_admin_umkm_respon',
        'tanggal_permintaan',
        'tanggal_respon',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'datetime',
        'tanggal_respon' => 'datetime',
        'harga_custom' => 'decimal:2',
    ];
}
