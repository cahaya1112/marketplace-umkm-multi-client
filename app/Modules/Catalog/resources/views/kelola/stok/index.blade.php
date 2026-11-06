@extends('catalog::kelola.layout')

@section('title', 'Monitoring Stok')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-[#232934]">Monitoring Stok</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">Stok produk milik {{ $umkm->nama }}</p>

    @include('catalog::kelola.partials.menu')

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total produk</p>
            <p class="mt-1 text-3xl font-bold">{{ $ringkasan['total'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Stok menipis</p>
            <p class="mt-1 text-3xl font-bold text-amber-600">{{ $ringkasan['MENIPIS'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Stok habis</p>
            <p class="mt-1 text-3xl font-bold text-red-600">{{ $ringkasan['HABIS'] }}</p>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach ([null => 'Semua', 'AMAN' => 'Aman', 'MENIPIS' => 'Menipis', 'HABIS' => 'Habis'] as $nilai => $label)
            <a href="{{ route('kelola.stok.index', $nilai ? ['status' => $nilai] : []) }}"
               class="rounded-lg px-4 py-1.5 font-medium {{ ($filter ?? null) === $nilai ? 'bg-[#3E5D3C] text-white' : 'bg-white hover:bg-[#E3D9BF]/60' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#E3D9BF]/50 text-xs uppercase text-slate-600">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Jumlah stok dan batas minimum</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $item)
                    @php($status = $item->stok?->status ?? 'HABIS')
                    <tr class="border-t border-[#E3D9BF]">
                        <td class="px-4 py-3 font-medium">{{ $item->nama }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $status === 'HABIS' ? 'bg-red-100 text-red-700' : ($status === 'MENIPIS' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('kelola.stok.update', $item->id_produk) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <input type="number" min="0" name="jumlah_stok" value="{{ $item->stok->jumlah_stok ?? 0 }}"
                                       class="w-24 rounded-lg border border-[#D2A874]/60 px-3 py-1.5" title="Jumlah stok">
                                <input type="number" min="0" name="batas_minimum" value="{{ $item->stok->batas_minimum ?? 5 }}"
                                       class="w-24 rounded-lg border border-[#D2A874]/60 px-3 py-1.5" title="Batas minimum">
                                <button type="submit" class="rounded-lg bg-[#3E5D3C] px-4 py-1.5 text-xs font-semibold text-white">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-10 text-center text-slate-500">Tidak ada data stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection