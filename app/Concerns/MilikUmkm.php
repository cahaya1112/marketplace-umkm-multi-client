<?php

namespace App\Concerns;

use App\Modules\Umkm\Umkm;
use Illuminate\Database\Eloquent\Builder;

/**
 * Query scope untuk model yang punya kolom id_umkm (mis. Produk, SubPesanan).
 * Dipakai di sisi UMKM agar query SELALU dibatasi ke UMKM yang sedang dikelola.
 *
 *   Produk::milikUmkm($umkm)->get();
 *   SubPesanan::milikUmkm($umkm)->where('status_sub_pesanan', 'DIPROSES')->get();
 */
trait MilikUmkm
{
    public function scopeMilikUmkm(Builder $query, Umkm|int $umkm): Builder
    {
        $idUmkm = $umkm instanceof Umkm ? $umkm->getKey() : $umkm;

        return $query->where($this->qualifyColumn('id_umkm'), $idUmkm);
    }
}