@php
    $induk = (auth()->check() && auth()->user()->isPembeli()) ? 'layouts.pelanggan' : 'layouts.publik';
@endphp
@extends($induk)

@section('title', $produk->nama)

@section('content')
@php
    $jumlahStok = $produk->stok ? $produk->stok->jumlah_stok : 0;
    $umkm = $produk->umkm;
    $logoUmkm = $umkm->gambar_logo
        ? (\Illuminate\Support\Str::startsWith($umkm->gambar_logo, ['http://', 'https://']) ? $umkm->gambar_logo : asset('storage/' . $umkm->gambar_logo))
        : null;
    $pengguna = auth()->user();
    $bolehCustom = $pengguna && $pengguna->isPembeli() && Route::has('custom.store');
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
        <span>←</span> Kembali ke Katalog
    </a>

    @if (session('success'))
        <div class="mt-4 rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session('galat'))
        <div class="mt-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">{{ session('galat') }}</div>
    @endif

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

            {{-- Tombol aksi --}}
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

                @if ($bolehCustom)
                    <button type="button" id="btn-custom" class="rounded-xl bg-[#c9a26c] px-6 py-3 font-semibold text-slate-900 hover:opacity-90">Ajukan Custom</button>
                @elseif (! $pengguna)
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="rounded-xl bg-[#c9a26c] px-6 py-3 font-semibold text-slate-900 hover:opacity-90">Ajukan Custom</a>
                @else
                    <button type="button" disabled title="Hanya pembeli yang dapat mengajukan custom"
                            class="cursor-not-allowed rounded-xl bg-[#c9a26c] px-6 py-3 font-semibold text-slate-900 opacity-50">Ajukan Custom</button>
                @endif
            </div>
        </div>
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

{{-- Pop-up Ajukan Custom (mengirim ke route custom.store milik modul Ordering) --}}
@if ($bolehCustom)
    <div id="modal-custom" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-[#f8ecd0] p-6 shadow-xl">
            <h2 class="text-xl font-bold text-slate-900">Ajukan Custom Pesanan</h2>
            <p class="mt-1 text-sm text-slate-600">Kirim spesifikasi khusus kepada Admin UMKM. Harga custom akan ditawarkan UMKM setelah permintaanmu diterima.</p>

            <div class="mt-4 rounded-xl bg-[#e2d9bd] p-4 text-sm">
                <p class="font-semibold">{{ $produk->nama }}</p>
                <p class="text-slate-600">Harga normal: Rp{{ number_format($produk->harga_retail, 0, ',', '.') }}</p>
            </div>

            <form method="POST" action="{{ route('custom.store', $produk->id_produk) }}" class="mt-4">
                @csrf
                <label class="mb-1 block text-sm font-medium">Spesifikasi custom</label>
                <textarea name="spesifikasi" rows="4" required minlength="10" placeholder="Contoh: warna hitam, ukuran 30x20 cm, motif polos"
                          class="w-full rounded-xl border border-[#c9a26c]/60 bg-white px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3d5a3d]">{{ old('spesifikasi') }}</textarea>
                @error('spesifikasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" id="batal-custom" class="rounded-xl border border-[#e2d9bd] bg-white px-5 py-2 text-sm font-medium">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#3d5a3d] px-5 py-2 text-sm font-semibold text-white">Ajukan Custom</button>
                </div>
            </form>
        </div>
    </div>
@endif

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

        var modal = document.getElementById('modal-custom');
        var tombol = document.getElementById('btn-custom');
        if (modal && tombol) {
            function buka() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function tutup() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
            tombol.addEventListener('click', buka);
            document.getElementById('batal-custom').addEventListener('click', tutup);
            modal.addEventListener('click', function (e) { if (e.target === modal) { tutup(); } });
            if ({{ $errors->has('spesifikasi') ? 'true' : 'false' }}) { buka(); }
        }
    })();
</script>
@endsection