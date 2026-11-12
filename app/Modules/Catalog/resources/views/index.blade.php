@php
    $induk = (auth()->check() && auth()->user()->isPembeli()) ? 'layouts.pelanggan' : 'layouts.publik';
@endphp
@extends($induk)

@section('title', 'Katalog Produk')

@section('content')
   <div class="mx-auto max-w-6xl px-4 py-12">

    {{-- Notifikasi berhasil --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi gagal --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <h1 class="text-4xl font-bold text-slate-900">Katalog Produk</h1>
    <p class="mt-3 text-lg text-slate-600">Temukan produk kerajinan UMKM lokal</p>

    {{-- Pencarian dan filter (FR-CUS-02) --}}
    <form method="GET" action="{{ route('katalog.index') }}" class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
               class="rounded-xl border border-[#c9a26c]/60 bg-white/60 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3d5a3d]">

        <select name="kategori" onchange="this.form.submit()"
                class="rounded-xl border border-[#c9a26c]/60 bg-white/60 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3d5a3d]">
            <option value="">Semua Kategori</option>
                          @foreach ($daftarKategori as $kat)
                   <option value="{{ $kat->id_kategori }}" @selected(request('kategori') == $kat->id_kategori)>{{ $kat->nama }}</option>
               @endforeach
        </select>

        <select name="umkm" onchange="this.form.submit()"
                class="rounded-xl border border-[#c9a26c]/60 bg-white/60 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3d5a3d]">
            <option value="">Semua UMKM</option>
            @foreach ($daftarUmkm as $umkm)
                <option value="{{ $umkm->id_umkm }}" @selected(request('umkm') == $umkm->id_umkm)>{{ $umkm->nama }}</option>
            @endforeach
        </select>

        <select name="ketersediaan" onchange="this.form.submit()"
                class="rounded-xl border border-[#c9a26c]/60 bg-white/60 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3d5a3d]">
            <option value="">Semua</option>
            <option value="tersedia" @selected(request('ketersediaan') === 'tersedia')>Tersedia</option>
            <option value="habis" @selected(request('ketersediaan') === 'habis')>Habis</option>
        </select>
    </form>

    {{-- Daftar produk (FR-CUS-01) --}}
    @if ($produk->isEmpty())
        <div class="mt-16 rounded-2xl bg-white/60 p-12 text-center">
            <p class="text-lg font-semibold">Produk tidak ditemukan</p>
            <p class="mt-1 text-slate-600">Coba ubah kata kunci atau filter yang dipilih.</p>
            <a href="{{ route('katalog.index') }}" class="mt-4 inline-block font-semibold text-[#3d5a3d] underline">Reset filter</a>
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($produk as $item)
                @php($jumlahStok = $item->stok->jumlah_stok ?? 0)
                <div class="flex flex-col overflow-hidden rounded-2xl border border-[#e2d9bd] bg-white shadow-sm transition hover:shadow-md">
                    <a href="{{ route('produk.show', $item->id_produk) }}" class="block">
                        <div class="aspect-square bg-[#e2d9bd]">
                            @if ($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm text-slate-500">Belum ada gambar</div>
                            @endif
                        </div>

                        <div class="px-5 pt-5">
                            <p class="text-sm font-medium text-[#c9a26c]">{{ $item->umkm->nama }}</p>
                            <h2 class="mt-1 font-semibold text-slate-900">{{ $item->nama }}</h2>
                            <p class="text-sm text-slate-500">{{ $item->kategori->nama }}</p>
                            <p class="mt-3 text-xl font-bold text-[#3d5a3d]">Rp{{ number_format($item->harga_retail, 0, ',', '.') }}</p>

                            <div class="mt-3 flex items-center gap-2 text-sm">
                                @if ($jumlahStok > 0)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 font-medium text-emerald-800">Tersedia</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 font-medium text-red-700">Habis</span>
                                @endif
                                <span class="text-slate-600">Stok: {{ $jumlahStok }}</span>
                            </div>
                        </div>
                    </a>

                    <div class="mt-auto flex gap-2 p-5">
                        {{-- Route keranjang.tambah dibuat oleh modul Keranjang (Anggota 3). --}}
                        @if ($jumlahStok > 0 && Route::has('keranjang.tambah'))
                            <form method="POST" action="{{ route('keranjang.tambah', $item->id_produk) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#3d5a3d] px-4 py-3 font-semibold text-white hover:opacity-90">
                                    @include('catalog::partials.ikon-keranjang')
                                    Keranjang
                                </button>
                            </form>
                        @else
                            <button type="button" disabled
                                    title="{{ $jumlahStok > 0 ? 'Fitur keranjang belum tersedia' : 'Stok habis' }}"
                                    class="flex flex-1 cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-[#3d5a3d] px-4 py-3 font-semibold text-white opacity-50">
                                @include('catalog::partials.ikon-keranjang')
                                Keranjang
                            </button>
                        @endif

                        <a href="{{ route('produk.show', $item->id_produk) }}" title="Lihat detail"
                           class="flex items-center justify-center rounded-xl border border-[#e2d9bd] px-4 py-3 text-slate-700 hover:bg-[#f8ecd0]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">{{ $produk->links() }}</div>
    @endif
</div>
@endsection