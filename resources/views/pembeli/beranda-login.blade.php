@extends('layouts.pelanggan')
@section('title', 'Beranda | Marketplace UMKM')

@section('content')
<section class="rounded-2xl bg-gradient-to-br from-forest to-[#2E6B4D] px-8 py-9">
<h1 class="text-2xl font-bold text-cream md:text-3xl">Selamat datang, {{ \Illuminate\Support\Str::words(auth()->user()->nama, 1, '') }}! 👋</h1>
<p class="mt-1 text-sm text-white/80">Temukan produk kerajinan UMKM lokal terbaik</p>
<form method="GET" action="{{ route('pembeli.home') }}" class="relative mt-5 max-w-md">
<span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><x-gi name="search" class="h-4 w-4" /></span>
<input type="search" name="q" value="{{ $q }}" placeholder="Cari produk..." class="h-10 w-full rounded-lg bg-white pl-9 pr-3 text-xs outline-none focus:ring-2 focus:ring-tan">
</form>
</section>

<section class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4">
@foreach ([['Katalog', 'box', '#katalog', 'bg-[#E6ECE6] text-forest'], ['Keranjang', 'cart', '#', 'bg-[#F6EADB] text-tan'], ['UMKM', 'store', '#umkm', 'bg-[#DCE8F1] text-[#447A9C]'], ['Kategori', 'tag', '#kategori', 'bg-[#E4ECE4] text-forest']] as [$nama, $ikon, $href, $warna])
<a href="{{ $href }}" class="flex flex-col items-center gap-2 rounded-xl border border-[#E8DFC6] bg-white px-4 py-5 shadow-sm transition hover:-translate-y-0.5">
<span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $warna }}"><x-gi :name="$ikon" class="h-4 w-4" /></span>
<span class="text-xs font-medium">{{ $nama }}</span>
</a>
@endforeach
</section>

<section id="kategori" class="mt-8">
<div class="flex items-end justify-between">
<h2 class="text-lg font-semibold">Kategori Populer</h2>
<a href="#katalog" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-4 grid gap-4 md:grid-cols-3">
@forelse ($kategori as $k)
<a href="#katalog" class="flex flex-col items-center gap-2 rounded-xl border border-[#E8DFC6] bg-white px-4 py-5 shadow-sm transition hover:-translate-y-0.5">
<span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F6EADB] text-tan"><x-gi name="tag" class="h-4 w-4" /></span>
<span class="text-xs font-medium">{{ $k->nama }}</span>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">Belum ada kategori.</p>
@endforelse
</div>
</section>

<section id="katalog" class="mt-8">
<div class="flex items-end justify-between">
<h2 class="text-lg font-semibold">{{ $q !== '' ? 'Hasil pencarian: “'.$q.'”' : 'Produk Terbaru' }}</h2>
<a href="{{ route('pembeli.home') }}#katalog" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-4 grid grid-cols-2 gap-5 md:grid-cols-4">
@forelse ($produk as $p)
<a href="#" class="overflow-hidden rounded-xl bg-white shadow-md shadow-black/5 transition hover:-translate-y-0.5">
@if ($p->gambar)
<img src="{{ $p->gambar }}" alt="{{ $p->nama }}" class="aspect-square w-full object-cover">
@else
<div class="aspect-square w-full bg-sand"></div>
@endif
<div class="px-3 py-3">
<div class="truncate text-xs">{{ $p->nama }}</div>
<div class="mt-0.5 text-xs font-semibold text-forest">Rp{{ number_format($p->harga_retail, 0, ',', '.') }}</div>
</div>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">{{ $q !== '' ? 'Produk tidak ditemukan.' : 'Belum ada produk.' }}</p>
@endforelse
</div>
</section>

<section id="umkm" class="mt-8 pb-8">
<div class="flex items-end justify-between">
<h2 class="text-lg font-semibold">UMKM Rekomendasi</h2>
<a href="#umkm" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-4 grid gap-5 md:grid-cols-3">
@forelse ($umkm as $u)
<a href="#" class="overflow-hidden rounded-xl bg-white shadow-md shadow-black/5 transition hover:-translate-y-0.5">
@if ($u->gambar_logo)
<img src="{{ $u->gambar_logo }}" alt="{{ $u->nama }}" class="h-20 w-full object-cover">
@else
<div class="h-20 bg-sand"></div>
@endif
<div class="px-4 py-3">
<div class="text-xs font-semibold">{{ $u->nama }}</div>
@if ($u->deskripsi)
<p class="mt-0.5 truncate text-[11px] text-gray-500">{{ $u->deskripsi }}</p>
@endif
</div>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">Belum ada UMKM terdaftar.</p>
@endforelse
</div>
</section>
@endsection