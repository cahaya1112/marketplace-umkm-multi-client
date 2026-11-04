@extends('layouts.publik')
@section('title', 'Marketplace Produk UMKM')

@section('content')
<section class="bg-gradient-to-br from-forest to-[#2E6B4D] pb-24 pt-20">
<div class="mx-auto max-w-6xl px-4">
<span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] text-white/90"><x-gi name="spark" class="h-3 w-3" />Kerajinan Lokal Indonesia</span>
<h1 class="mt-5 text-4xl font-bold text-cream md:text-5xl">Marketplace Produk UMKM</h1>
<p class="mt-4 max-w-md text-sm font-light leading-relaxed text-white/80">Temukan produk kerajinan bambu, rotan, dan furnitur berkualitas dari UMKM lokal. Satu platform untuk banyak UMKM, dengan fitur custom pesanan khusus.</p>
<div class="mt-7 flex flex-wrap gap-3 text-xs">
<a href="#katalog" class="inline-flex items-center gap-2 rounded-lg bg-tan px-5 py-2.5 font-medium text-ink hover:brightness-95">Lihat Katalog <x-gi name="arrow" class="h-3.5 w-3.5" /></a>
<a href="#umkm" class="rounded-lg border border-white/30 px-5 py-2.5 font-medium text-white hover:bg-white/10">Jelajahi UMKM</a>
</div>
</div>
</section>

<section class="mx-auto -mt-12 max-w-6xl px-4">
<div class="grid grid-cols-2 gap-4 md:grid-cols-4">
@foreach ($stats as $s)
<div class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-lg shadow-black/5">
<span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sand text-forest"><x-gi :name="$s['icon']" class="h-5 w-5" /></span>
<div>
<div class="text-lg font-semibold leading-tight">{{ number_format($s['value']) }}</div>
<div class="text-[11px] text-gray-500">{{ $s['label'] }}</div>
</div>
</div>
@endforeach
</div>
</section>

<section id="kategori" class="mx-auto max-w-6xl px-4 pt-16">
<div class="flex items-end justify-between">
<div>
<h2 class="text-lg font-semibold">Kategori Populer</h2>
<p class="text-xs text-gray-500">Jelajahi produk berdasarkan kategori</p>
</div>
<a href="#katalog" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-5 grid gap-5 md:grid-cols-3">
@forelse ($kategori as $k)
<a href="#katalog" class="overflow-hidden rounded-xl bg-white shadow-md shadow-black/5 transition hover:-translate-y-0.5">
<div class="flex h-28 items-center justify-center bg-gradient-to-b from-[#EFE6D6] to-[#DDE0D8] text-gray-400"><x-gi name="tag" class="h-8 w-8" /></div>
<div class="px-4 py-3">
<div class="text-sm font-semibold">{{ $k->nama }}</div>
<div class="text-[11px] text-gray-500">Lihat produk &rarr;</div>
</div>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">Belum ada kategori.</p>
@endforelse
</div>
</section>

<section id="katalog" class="mx-auto max-w-6xl px-4 pt-16">
<div class="flex items-end justify-between">
<div>
<h2 class="text-lg font-semibold">Produk Unggulan</h2>
<p class="text-xs text-gray-500">Pilihan terbaik dari UMKM lokal</p>
</div>
<a href="#katalog" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-5 grid grid-cols-2 gap-5 md:grid-cols-4">
@forelse ($produk as $p)
<a href="#" class="overflow-hidden rounded-xl bg-white shadow-md shadow-black/5 transition hover:-translate-y-0.5">
@if ($p->gambar)
<img src="{{ $p->gambar }}" alt="{{ $p->nama }}" class="aspect-[4/5] w-full object-cover">
@else
<div class="aspect-[4/5] w-full bg-sand"></div>
@endif
<div class="px-3 py-3">
<div class="truncate text-xs">{{ $p->nama }}</div>
<div class="mt-0.5 text-xs font-semibold text-forest">Rp{{ number_format($p->harga_retail, 0, ',', '.') }}</div>
</div>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">Belum ada produk.</p>
@endforelse
</div>
</section>

<section id="umkm" class="mx-auto max-w-6xl px-4 pb-20 pt-16">
<div class="flex items-end justify-between">
<div>
<h2 class="text-lg font-semibold">UMKM Terdaftar</h2>
<p class="text-xs text-gray-500">Mitra UMKM di platform kami</p>
</div>
<a href="#umkm" class="text-xs text-[#447A9C] hover:underline">Lihat Semua &rarr;</a>
</div>
<div class="mt-5 grid gap-5 md:grid-cols-3">
@forelse ($umkm as $u)
<a href="#" class="overflow-hidden rounded-xl bg-white shadow-md shadow-black/5 transition hover:-translate-y-0.5">
@if ($u->gambar_logo)
<img src="{{ $u->gambar_logo }}" alt="{{ $u->nama }}" class="h-24 w-full object-cover">
@else
<div class="h-24 bg-sand"></div>
@endif
<div class="p-4">
<div class="text-sm font-semibold">{{ $u->nama }}</div>
@if ($u->deskripsi)
<p class="mt-1 text-[11px] leading-relaxed text-gray-500">{{ \Illuminate\Support\Str::limit($u->deskripsi, 110) }}</p>
@endif
@if ($u->alamat)
<p class="mt-2 flex items-center gap-1 text-[11px] text-gray-500"><x-gi name="pin" class="h-3 w-3 text-red-500" />{{ $u->alamat }}</p>
@endif
</div>
</a>
@empty
<p class="col-span-full rounded-xl bg-white/60 p-6 text-center text-xs text-gray-500">Belum ada UMKM terdaftar.</p>
@endforelse
</div>
</section>
@endsection