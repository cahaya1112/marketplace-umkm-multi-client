<?php

namespace App\Modules\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stok extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'id_stok';
    public $timestamps = false;

    protected $fillable = ['id_produk', 'jumlah_stok', 'batas_minimum'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Status dihitung dari batas_minimum, tidak disimpan: HABIS / MENIPIS / AMAN.
    public function getStatusAttribute(): string
    {
        if ($this->jumlah_stok <= 0) {
            return 'HABIS';
        }

        return $this->jumlah_stok <= $this->batas_minimum ? 'MENIPIS' : 'AMAN';
    }
}