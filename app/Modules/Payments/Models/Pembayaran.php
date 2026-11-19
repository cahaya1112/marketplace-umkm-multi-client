<?php

namespace App\Modules\Payments\Models;

use App\Modules\Ordering\Models\Pesanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'order_id_gateway',
        'id_transaksi_gateway',
        'metode_pembayaran',
        'jumlah_bayar',
        'status_pembayaran',
        'token_pembayaran',
        'payload_notifikasi',
        'expired_at',
        'waktu_bayar',
    ];

    protected $casts = [
        'payload_notifikasi' => 'array',
        'expired_at' => 'datetime',
        'waktu_bayar' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            Pesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}
