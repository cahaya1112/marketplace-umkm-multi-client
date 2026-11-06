<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\Kategori;
use App\Modules\Catalog\Produk;
use App\Modules\Catalog\Stok;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProdukController extends KelolaController
{
    public function index(Request $request)
    {
        $umkm = $this->umkmSaya();

        $produk = Produk::query()
            ->with(['kategori', 'stok'])
            ->where('id_umkm', $umkm->id_umkm)
            ->cari($request->query('q'))
            ->orderByDesc('id_produk')
            ->paginate(10)
            ->withQueryString();

        return view('catalog::kelola.produk.index', compact('umkm', 'produk'));
    }

    public function create()
    {
        $this->umkmSaya();

        return view('catalog::kelola.produk.form', [
            'produk'         => new Produk(),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $umkm = $this->umkmSaya();
        $data = $this->validasi($request);

        DB::transaction(function () use ($request, $umkm, $data) {
            $produk = new Produk(Arr::only($data, ['id_kategori', 'nama', 'deskripsi', 'harga_retail', 'berat_gram']));
            $produk->id_umkm = $umkm->id_umkm; // dari server, bukan dari form
            $produk->gambar = $this->simpanFile($request, 'gambar', 'produk') ?? '';
            $produk->save();

            Stok::create([
                'id_produk'     => $produk->id_produk,
                'jumlah_stok'   => $data['jumlah_stok'],
                'batas_minimum' => $data['batas_minimum'],
            ]);
        });

        return redirect()->route('kelola.produk.index')->with('sukses', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $produk = $this->produkMilikku($id);

        return view('catalog::kelola.produk.form', [
            'produk'         => $produk,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $produk = $this->produkMilikku($id);
        $data = $this->validasi($request);

        DB::transaction(function () use ($request, $produk, $data) {
            $produk->fill(Arr::only($data, ['id_kategori', 'nama', 'deskripsi', 'harga_retail', 'berat_gram']));

            if ($request->hasFile('gambar')) {
                $this->hapusFile($produk->gambar);
                $produk->gambar = $this->simpanFile($request, 'gambar', 'produk');
            }

            $produk->save();

            Stok::updateOrCreate(
                ['id_produk' => $produk->id_produk],
                ['jumlah_stok' => $data['jumlah_stok'], 'batas_minimum' => $data['batas_minimum']]
            );
        });

        return redirect()->route('kelola.produk.index')->with('sukses', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $produk = $this->produkMilikku($id);

        try {
            DB::transaction(function () use ($produk) {
                Stok::where('id_produk', $produk->id_produk)->delete();
                $produk->delete();
            });
        } catch (QueryException $e) {
            return back()->with('galat', 'Produk tidak dapat dihapus karena sudah dipakai di keranjang atau pesanan.');
        }

        $this->hapusFile($produk->gambar);

        return redirect()->route('kelola.produk.index')->with('sukses', 'Produk berhasil dihapus.');
    }

    // Hanya produk milik UMKM yang login; produk UMKM lain menghasilkan 404.
    private function produkMilikku(int $id): Produk
    {
        $umkm = $this->umkmSaya();

        return Produk::with('stok')->where('id_umkm', $umkm->id_umkm)->findOrFail($id);
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:150'],
            'id_kategori'   => ['required', 'integer', 'exists:kategori,id_kategori'],
            'deskripsi'     => ['nullable', 'string', 'max:2000'],
            'harga_retail'  => ['required', 'integer', 'min:0', 'max:1000000000'],
            'berat_gram'    => ['required', 'integer', 'min:1', 'max:1000000'],
            'jumlah_stok'   => ['required', 'integer', 'min:0', 'max:1000000'],
            'batas_minimum' => ['required', 'integer', 'min:0', 'max:1000000'],
            'gambar'        => ['nullable', 'image', 'max:2048'],
        ]);

        $data['deskripsi'] = $data['deskripsi'] ?? '';

        return $data;
    }
}