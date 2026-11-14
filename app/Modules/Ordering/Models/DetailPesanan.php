<?php

namespace App\Modules\Ordering\Models;

use App\Modules\Catalog\Produk;
use App\Modules\Catalog\PermintaanCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $primaryKey = 'id_detail_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_sub_pesanan',
        'id_produk',
        'id_custom',
        'harga_retail',
        'harga_custom',
        'jumlah',
        'nama_produk_snapshot',
        'spesifikasi_custom_snapshot',
    ];

    public function subPesanan(): BelongsTo
    {
        return $this->belongsTo(
            SubPesanan::class,
            'id_sub_pesanan',
            'id_sub_pesanan'
        );
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(
            Produk::class,
            'id_produk',
            'id_produk'
        );
    }

    public function permintaanCustom(): BelongsTo
    {
        return $this->belongsTo(
            PermintaanCustom::class,
            'id_custom',
            'id_custom'
        );
    }
}