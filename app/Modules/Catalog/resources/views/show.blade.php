   @extends('layouts.publik')

@section('title', $produk->nama)

@section('content')
@php
    $jumlahStok = $produk->stok->jumlah_stok ?? 0;
    $umkm = $produk->umkm;
    $logoUmkm = $umkm->gambar_logo
        ? (\Illuminate\Support\Str::startsWith($umkm->gambar_logo, ['http://', 'https://']) ? $umkm->gambar_logo : asset('storage/' . $umkm->gambar_logo))
        : null;
@endphp

   <div class="mx-auto max-w-6xl px-4 py-10">
    <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
        <span>←</span> Kembali ke Katalog
    </a>

    <div class="mt-6 grid gap-10 lg:grid-cols-2">
        {{-- Gambar produk --}}
        <div class="aspect-square overflow-hidden rounded-3xl bg-white shadow-sm">
            @if ($produk->gambar_url)
                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full w-full items-center justify-center text-slate-400">Belum ada gambar</div>
            @endif
        </div>

        {{-- Info produk --}}
        <div>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="rounded-full bg-[#c9a26c]/20 px-3 py-1 font-medium text-[#a07a45]">{{ $produk->kategori->nama }}</span>
                @if ($jumlahStok > 0)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 font-medium text-emerald-800">Tersedia</span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-1 font-medium text-red-700">Habis</span>
                @endif
            </div>

            <h1 class="mt-3 text-4xl font-bold text-slate-900">{{ $produk->nama }}</h1>
            <a href="{{ url('/umkm/' . $umkm->id_umkm) }}" class="mt-2 inline-block font-medium text-sky-700 hover:underline">{{ $umkm->nama }}</a>

            <p class="mt-5 text-4xl font-bold text-[#3d5a3d]">Rp{{ number_format($produk->harga_retail, 0, ',', '.') }}</p>

            <hr class="my-6 border-[#e2d9bd]">

            <h2 class="text-lg font-semibold text-slate-900">Deskripsi</h2>
            <p class="mt-2 text-slate-600">{{ $produk->deskripsi ?: 'Belum ada deskripsi.' }}</p>

            <p class="mt-6 text-slate-600">
                Stok: <span class="font-semibold text-slate-900">{{ $jumlahStok }}</span>
                <span class="ml-4">UMKM: <span class="font-semibold text-slate-900">{{ $umkm->nama }}</span></span>
            </p>

            {{-- Jumlah --}}
            <div class="mt-6 flex items-center gap-4">
                <span class="font-medium">Jumlah:</span>
                <div class="inline-flex items-center rounded-xl border border-[#e2d9bd] bg-white">
                    <button type="button" id="btn-kurang" @disabled($jumlahStok < 1) class="px-4 py-3 text-lg disabled:opacity-40">−</button>
                    <span id="jumlah-tampil" class="w-10 text-center font-medium">1</span>
                    <button type="button" id="btn-tambah" @disabled($jumlahStok < 1) class="px-4 py-3 text-lg disabled:opacity-40">+</button>
                </div>
            </div>

            {{-- Tombol aksi. keranjang.tambah dan checkout.langsung dibuat oleh modul
                 Keranjang/Checkout (Anggota 3); selama belum ada, tombolnya nonaktif. --}}
            <div class="mt-5 flex flex-wrap gap-3">
                @if ($jumlahStok > 0 && Route::has('keranjang.tambah'))
                    <form method="POST" action="{{ route('keranjang.tambah', $produk->id_produk) }}">
                        @csrf
                        <input type="hidden" name="jumlah" value="1">
                        <button type="submit" class="rounded-xl border border-[#e2d9bd] bg-white px-6 py-3 font-semibold hover:bg-[#f8ecd0]">Tambah ke Keranjang</button>
                    </form>
                @else
                    <button type="button" disabled class="cursor-not-allowed rounded-xl border border-[#e2d9bd] bg-white px-6 py-3 font-semibold opacity-50"
                            title="{{ $jumlahStok > 0 ? 'Fitur keranjang belum tersedia' : 'Stok habis' }}">Tambah ke Keranjang</button>
                @endif

                @if ($jumlahStok > 0 && Route::has('checkout.langsung'))
                    <form method="POST" action="{{ route('checkout.langsung', $produk->id_produk) }}">
                        @csrf
                        <input type="hidden" name="jumlah" value="1">
                        <button type="submit" class="rounded-xl bg-[#3d5a3d] px-6 py-3 font-semibold text-white hover:opacity-90">Beli Sekarang</button>
                    </form>
                @else
                    <button type="button" disabled class="cursor-not-allowed rounded-xl bg-[#3d5a3d] px-6 py-3 font-semibold text-white opacity-50"
                            title="{{ $jumlahStok > 0 ? 'Fitur checkout belum tersedia' : 'Stok habis' }}">Beli Sekarang</button>
                @endif

                <button type="button" onclick="document.getElementById('form-custom').classList.toggle('hidden')" class="rounded-xl bg-[#c9a26c] px-6 py-3 font-semibold text-slate-900 hover:opacity-90"> Ajukan Custom
                </button>
            </div>
        </div>
    </div>

    {{-- Form pengajuan custom --}}
<div id="form-custom" class="mt-6 hidden rounded-2xl border border-[#e2d9bd] bg-white p-6 shadow-sm">
    <h2 class="text-xl font-bold text-slate-900">Ajukan Custom Pesanan</h2>

    <p class="mt-2 text-sm text-slate-600">
        Produk: {{ $produk->nama }}
    </p>

    <p class="mt-1 text-sm text-slate-600">
        Tuliskan detail ukuran, warna, motif, bahan, dan kebutuhan custom lainnya.
        UMKM akan meninjau permintaan dan menentukan harga.
    </p>

    <form method="POST" action="{{ route('custom.store', $produk->id_produk) }}" class="mt-4 space-y-4">
        @csrf

        <textarea
            name="spesifikasi"
            rows="5"
            required
            minlength="10"
            maxlength="3000"
            placeholder="Contoh: Saya ingin lampu rotan ukuran 40 cm, warna natural, dengan motif minimalis..."
            class="w-full rounded-xl border border-[#e2d9bd] px-4 py-3 text-sm focus:border-[#3d5a3d] focus:outline-none"
        >{{ old('spesifikasi') }}</textarea>

        @error('spesifikasi')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex flex-wrap gap-3">
            <button
                type="submit"
                class="rounded-xl bg-[#3d5a3d] px-6 py-3 font-semibold text-white hover:opacity-90">
                Kirim Permintaan
            </button>

            <button
                type="button"
                onclick="document.getElementById('form-custom').classList.add('hidden')"
                class="rounded-xl border border-slate-300 px-6 py-3 font-semibold text-slate-600">
                Batal
            </button>
        </div>
    </form>
</div>

    {{-- Kartu toko --}}
    <div class="mt-12 flex flex-col gap-4 rounded-2xl border border-[#e2d9bd] bg-white p-6 shadow-sm sm:flex-row sm:items-center">
        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-[#e2d9bd]">
            @if ($logoUmkm)
                <img src="{{ $logoUmkm }}" alt="{{ $umkm->nama }}" class="h-full w-full object-cover">
            @endif
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-semibold text-slate-900">{{ $umkm->nama }}</h3>
            <p class="mt-1 text-slate-600">{{ $umkm->deskripsi }}</p>
            <p class="mt-2 text-sm text-slate-500">📍 {{ $umkm->alamat }}</p>
        </div>
        <a href="{{ url('/umkm/' . $umkm->id_umkm) }}" class="rounded-xl border border-[#e2d9bd] px-5 py-3 font-semibold hover:bg-[#f8ecd0]">Lihat Toko</a>
    </div>
</div>

{{-- Notifikasi (toast) --}}

<script>
    (function () {
        var stok = {{ (int) $jumlahStok }};
        var jumlah = 1;
        var tampil = document.getElementById('jumlah-tampil');

        function render() {
            tampil.textContent = jumlah;
            document.querySelectorAll('input[name="jumlah"]').forEach(function (el) { el.value = jumlah; });
        }

        document.getElementById('btn-kurang').addEventListener('click', function () {
            if (jumlah > 1) { jumlah--; render(); }
        });
        document.getElementById('btn-tambah').addEventListener('click', function () {
            if (jumlah < stok) { jumlah++; render(); }
        });

        var toast = document.getElementById('toast');
        document.getElementById('btn-custom').addEventListener('click', function () {
            toast.classList.remove('hidden');
            clearTimeout(window.__toastTimer);
            window.__toastTimer = setTimeout(function () { toast.classList.add('hidden'); }, 3500);
        });
    })();
</script>
@endsection