<?php

namespace App\Modules\Catalog;

use App\Concerns\DicatatAuditLog;
use App\Modules\Umkm\Umkm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Produk extends Model
{
    // Tambah, ubah, dan hapus produk masuk audit log (FR-LOG-01).
    use DicatatAuditLog;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    // id_umkm sengaja TIDAK masuk fillable: nilainya diisi server dari UMKM milik
    // pengguna yang login (auth()->user()->umkm), bukan dari input form (NFR-MC-04).
    protected $fillable = [
        'id_kategori', 'nama', 'deskripsi',
        'harga_retail', 'berat_gram', 'gambar',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function stok(): HasOne
    {
        return $this->hasOne(Stok::class, 'id_produk', 'id_produk');
    }

    // URL gambar: mendukung path di storage publik maupun URL penuh.
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return Str::startsWith($this->gambar, ['http://', 'https://'])
            ? $this->gambar
            : asset('storage/' . $this->gambar);
    }

    // Katalog publik: hanya produk dari UMKM berstatus AKTIF.
    public function scopeTampilDiKatalog(Builder $query): Builder
    {
        return $query->whereHas('umkm', fn ($q) => $q->where('status', 'AKTIF'));
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->whereHas('stok', fn ($q) => $q->where('jumlah_stok', '>', 0));
    }

    // Stok habis, termasuk produk yang belum punya baris stok.
    public function scopeHabis(Builder $query): Builder
    {
        return $query->whereDoesntHave('stok', fn ($q) => $q->where('jumlah_stok', '>', 0));
    }

    public function scopeCari(Builder $query, ?string $kataKunci): Builder
    {
        return $kataKunci
            ? $query->where('nama', 'like', '%' . $kataKunci . '%')
            : $query;
    }
}