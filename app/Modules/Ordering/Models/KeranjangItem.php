<?php

namespace App\Modules\Ordering\Models;

use App\Modules\Catalog\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Catalog\PermintaanCustom;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KeranjangItem extends Model
{
    protected $table = 'keranjang_item';

    protected $primaryKey = 'id_keranjang_item';

    public $timestamps = false;

    protected $fillable = [
        'id_keranjang',
        'id_produk',
        'jumlah',
    ];

    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(
            Keranjang::class,
            'id_keranjang',
            'id_keranjang'
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

    public function permintaanCustom(): HasOne
    {
        return $this->hasOne(
            PermintaanCustom::class,
            'id_keranjang_item',
            'id_keranjang_item'
        );
    }
}