<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\Kategori;
use App\Modules\Catalog\Produk;
use App\Modules\Umkm\Umkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogApiController
{
    public function produk(Request $request): JsonResponse
    {
        $request->validate([
            'q'            => ['nullable', 'string', 'max:100'],
            'kategori'     => ['nullable', 'integer'],
            'umkm'         => ['nullable', 'integer'],
            'ketersediaan' => ['nullable', 'in:tersedia,habis'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $paginator = Produk::query()
            ->with(['umkm', 'kategori', 'stok'])
            ->tampilDiKatalog()
            ->cari($request->query('q'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('id_kategori', $request->integer('kategori')))
            ->when($request->filled('umkm'), fn ($q) => $q->where('id_umkm', $request->integer('umkm')))
            ->when($request->query('ketersediaan') === 'tersedia', fn ($q) => $q->tersedia())
            ->when($request->query('ketersediaan') === 'habis', fn ($q) => $q->habis())
            ->orderByDesc('id_produk')
            ->paginate($request->integer('per_page', 12))
            ->withQueryString();

        return response()->json([
            'data' => $paginator->getCollection()->map(fn ($p) => $this->produkArray($p))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function detailProduk(int $id): JsonResponse
    {
        $produk = Produk::with(['umkm', 'kategori', 'stok'])->tampilDiKatalog()->find($id);

        if (! $produk) {
            return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $this->produkArray($produk)]);
    }

    public function kategori(): JsonResponse
    {
        return response()->json([
            'data' => Kategori::orderBy('nama')->get()->map(fn ($k) => [
                'id_kategori' => $k->id_kategori,
                'nama'        => $k->nama,
            ])->values(),
        ]);
    }

    public function umkm(int $id): JsonResponse
    {
        $umkm = Umkm::where('status', 'AKTIF')->find($id);

        if (! $umkm) {
            return response()->json(['message' => 'Toko tidak ditemukan.'], 404);
        }

        return response()->json(['data' => [
            'id_umkm'       => $umkm->id_umkm,
            'nama'          => $umkm->nama,
            'deskripsi'     => $umkm->deskripsi,
            'alamat'        => $umkm->alamat,
            'kontak'        => $umkm->kontak,
            'logo'          => $this->urlFile($umkm->gambar_logo),
            'jumlah_produk' => Produk::where('id_umkm', $umkm->id_umkm)->count(),
        ]]);
    }

    private function produkArray(Produk $p): array
    {
        $stok = $p->stok->jumlah_stok ?? 0;

        return [
            'id_produk'    => $p->id_produk,
            'nama'         => $p->nama,
            'deskripsi'    => $p->deskripsi,
            'harga_retail' => (int) $p->harga_retail,
            'berat_gram'   => (int) $p->berat_gram,
            'gambar'       => $p->gambar_url,
            'kategori'     => $p->kategori ? ['id_kategori' => $p->kategori->id_kategori, 'nama' => $p->kategori->nama] : null,
            'umkm'         => $p->umkm ? ['id_umkm' => $p->umkm->id_umkm, 'nama' => $p->umkm->nama] : null,
            'stok'         => (int) $stok,
            'tersedia'     => $stok > 0,
        ];
    }

    private function urlFile(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/' . $path);
    }
}