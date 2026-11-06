<?php

namespace App\Modules\Catalog;

use App\Concerns\DicatatAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanCustom extends Model
{
    use DicatatAuditLog;

    protected $table = 'permintaan_custom';
    protected $primaryKey = 'id_custom';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna', 'id_produk', 'id_keranjang_item',
        'spesifikasi', 'harga_custom', 'status_custom', 'catatan_umkm',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pembeli(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}