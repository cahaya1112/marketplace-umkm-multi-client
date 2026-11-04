@extends('layouts.dasbor')
@section('title', 'Dashboard Super Admin')

@section('content')
<h1 class="text-2xl font-bold">Dashboard Super Admin</h1>
<p class="text-sm text-gray-500">Ringkasan platform marketplace UMKM</p>

<div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
@foreach ($stats as $s)
<x-kartu-statistik :icon="$s['icon']" :value="$s['value']" :label="$s['label']" :warna="$s['warna']" />
@endforeach
</div>

<section class="mt-5 rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="flex items-center gap-2 text-sm font-semibold"><x-gi name="chart" class="h-4 w-4 text-forest" />Penjualan per UMKM</h2>

@if ($grafik->isEmpty())
<p class="py-10 text-center text-xs text-gray-500">Belum ada UMKM.</p>
@else
<div class="mt-6 flex">
<div class="relative h-64 w-12 shrink-0 text-[10px] text-gray-500">
@foreach ($ticks as $i => $t)
<span class="absolute right-2 translate-y-1/2" style="bottom: {{ $i * 25 }}%">{{ $t }}</span>
@endforeach
</div>

<div class="min-w-0 flex-1">
<div class="relative h-64 border-b border-l border-gray-400/60">
@foreach ($ticks as $i => $t)
@if ($i > 0)
<div class="absolute inset-x-0 border-t border-dashed border-[#D9D0B6]" style="bottom: {{ $i * 25 }}%"></div>
@endif
@endforeach
<div class="absolute inset-0 grid" style="grid-template-columns: repeat({{ $grafik->count() }}, minmax(0, 1fr)); grid-template-rows: 100%;">
@foreach ($grafik as $g)
<div class="flex h-full items-end justify-center px-[8%]" title="{{ $g['nama'] }}: Rp{{ number_format($g['total'], 0, ',', '.') }}">
<div class="w-full rounded-t-lg bg-forest" style="height: {{ $g['persen'] }}%"></div>
</div>
@endforeach
</div>
</div>
<div class="grid pt-2 text-center text-[11px] text-gray-500" style="grid-template-columns: repeat({{ $grafik->count() }}, minmax(0, 1fr));">
@foreach ($grafik as $g)
<div class="truncate px-1">{{ $g['nama'] }}</div>
@endforeach
</div>
</div>
</div>
@endif
</section>

<section class="mt-5 rounded-xl border border-[#E8DFC6] bg-white p-6 shadow-sm">
<h2 class="text-sm font-semibold">UMKM Terbaru</h2>
<ul class="mt-4 divide-y divide-[#EFE7D2]">
@forelse ($umkmTerbaru as $u)
<li class="flex items-center gap-3 py-3">
@if ($u->gambar_logo)
<img src="{{ $u->gambar_logo }}" alt="{{ $u->nama }}" class="h-10 w-10 rounded-lg object-cover">
@else
<span class="h-10 w-10 rounded-lg bg-sand"></span>
@endif
<div class="min-w-0 flex-1">
<div class="truncate text-sm">{{ $u->nama }}</div>
<div class="truncate text-[11px] text-gray-500">{{ $u->alamat ?: '-' }}</div>
</div>
<x-lencana :status="$u->status" />
</li>
@empty
<li class="py-6 text-center text-xs text-gray-500">Belum ada UMKM.</li>
@endforelse
</ul>
</section>
@endsection