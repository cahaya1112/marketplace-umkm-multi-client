@extends('catalog::kelola.layout')

@section('title', 'Kelola Produk')

@section('content')
@php
    $warnaStatus = [
        'HABIS'   => 'bg-red-100 text-red-700',
        'MENIPIS' => 'bg-amber-100 text-amber-700',
        'AMAN'    => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="w-full">
    <h1 class="text-3xl font-bold">Kelola Produk</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">Produk milik {{ $umkm->nama }}</p>

    @include('catalog::kelola.partials.menu')

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                   class="rounded-xl border border-[#D2A874]/60 bg-white px-4 py-2 text-sm">
            <button type="submit" class="rounded-xl bg-[#E3D9BF] px-4 py-2 text-sm font-medium">Cari</button>
        </form>
        <a href="{{ route('kelola.produk.create') }}" class="rounded-xl bg-[#3E5D3C] px-5 py-2 text-sm font-semibold text-white">+ Tambah Produk</a>
    </div>

    @if ($produk->count() === 0)
        <div class="rounded-2xl bg-white p-10 text-center text-slate-500 shadow-sm">Belum ada produk.</div>
    @else
        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#E3D9BF]/50 text-xs uppercase text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produk as $item)
                        @php
                            $jumlah = $item->stok ? $item->stok->jumlah_stok : 0;
                            $status = $item->stok ? $item->stok->status : 'HABIS';
                            $kelasStatus = $warnaStatus[$status] ?? $warnaStatus['AMAN'];
                            $namaKategori = $item->kategori ? $item->kategori->nama : '-';
                        @endphp
                        <tr class="border-t border-[#E3D9BF]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-[#E3D9BF]">
                                        @if ($item->gambar_url)
                                            <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <span class="font-medium">{{ $item->nama }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $namaKategori }}</td>
                            <td class="px-4 py-3">Rp{{ number_format($item->harga_retail, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                {{ $jumlah }}
                                <span class="ml-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $kelasStatus }}">{{ $status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('kelola.produk.edit', $item->id_produk) }}" class="font-medium text-[#3E5D3C] hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('kelola.produk.destroy', $item->id_produk) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $produk->links() }}</div>
    @endif
</div>
@endsection