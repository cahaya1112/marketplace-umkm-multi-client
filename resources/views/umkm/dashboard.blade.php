@extends('layouts.dasbor')
@section('title', 'Dashboard UMKM: '.$umkm->nama)

@section('content')
<h1 class="text-2xl font-bold">Dashboard</h1>
<p class="text-sm text-gray-500">UMKM: {{ $umkm->nama }}</p>

<div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
@foreach ($stats as $s)
<x-kartu-statistik :icon="$s['icon']" :value="$s['value']" :label="$s['label']" :warna="$s['warna']" />
@endforeach
</div>

<div class="mt-6 grid gap-5 lg:grid-cols-2">

<section class="rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="text-sm font-semibold">Pesanan Terbaru</h2>
<ul class="mt-4 space-y-4">
@forelse ($pesananTerbaru as $p)
<li class="flex items-center justify-between gap-3">
<div>
<div class="text-xs font-medium">{{ $p->kode_pesanan }}</div>
<div class="text-[11px] text-gray-500">{{ $p->pembeli }} · {{ \Illuminate\Support\Carbon::parse($p->dibuat_pada)->locale('id')->translatedFormat('d M Y, H:i') }}</div>
</div>
<x-lencana :status="$p->status" />
</li>
@empty
<li class="py-6 text-center text-xs text-gray-500">Belum ada pesanan.</li>
@endforelse
</ul>
</section>

<section class="rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="text-sm font-semibold">Custom Terbaru</h2>
<ul class="mt-4 space-y-4">
@forelse ($customTerbaru as $c)
<li class="flex items-center justify-between gap-3">
<div>
<div class="text-xs font-medium">{{ $c->produk }}</div>
<div class="text-[11px] text-gray-500">{{ $c->pembeli }}</div>
</div>
<x-lencana :status="$c->status" />
</li>
@empty
<li class="py-6 text-center text-xs text-gray-500">Belum ada permintaan custom.</li>
@endforelse
</ul>
</section>

<section class="rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="text-sm font-semibold">Retur Terbaru</h2>
<ul class="mt-4 space-y-4">
@forelse ($returTerbaru as $r)
<li class="flex items-center justify-between gap-3">
<div>
<div class="text-xs font-medium">{{ \Illuminate\Support\Str::limit($r->alasan, 48) }}</div>
<div class="text-[11px] text-gray-500">{{ $r->pembeli }}</div>
</div>
<x-lencana :status="$r->status" />
</li>
@empty
<li class="py-6 text-center text-xs text-gray-500">Belum ada retur.</li>
@endforelse
</ul>
</section>

<section class="rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="flex items-center gap-2 text-sm font-semibold"><x-gi name="bell" class="h-4 w-4" />Notifikasi</h2>
<ul class="mt-4 space-y-2">
@forelse ($notifikasi as $n)
<li class="rounded-lg bg-[#F4F4F0] px-3 py-2">
<div class="text-xs font-medium">{{ $n['judul'] }}</div>
<div class="text-[11px] text-gray-500">{{ $n['isi'] }}</div>
</li>
@empty
<li class="py-6 text-center text-xs text-gray-500">Belum ada notifikasi.</li>
@endforelse
</ul>
</section>

</div>
@endsection