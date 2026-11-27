@extends('catalog::kelola.layout')

@section('title', 'Pesanan UMKM')

@section('content')
@php
    $warnaStatus = [
        'MENUNGGU_PEMBAYARAN' => 'bg-amber-100 text-amber-800',
        'DIPROSES' => 'bg-blue-100 text-blue-800',
        'DIKIRIM' => 'bg-violet-100 text-violet-800',
        'SELESAI' => 'bg-emerald-100 text-emerald-800',
        'DIBATALKAN' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="w-full">
    <h1 class="text-3xl font-bold">Kelola Pesanan</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">Daftar pesanan yang masuk ke UMKM {{ $umkm->nama }}.</p>

    @include('catalog::kelola.partials.menu')

    @if ($subPesanan->isEmpty())
        <div class="rounded-2xl bg-white p-10 text-center text-slate-500 shadow-sm">
            Belum ada pesanan untuk UMKM ini.
        </div>
    @else
        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#E3D9BF]/50 text-xs uppercase text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Kode Pesanan</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Pengiriman</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subPesanan as $item)
                        @php
                            $status = $item->status_sub_pesanan;
                            $kelasStatus = $warnaStatus[$status] ?? 'bg-slate-100 text-slate-700';
                            $pesanan = $item->pesanan;
                        @endphp
                        <tr class="border-t border-[#E3D9BF]">
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $pesanan?->kode_pesanan ?? 'Pesanan #' . $item->id_pesanan }}</div>
                                <div class="mt-1 text-xs text-slate-500">Subpesanan #{{ $item->id_sub_pesanan }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $pesanan?->nama_penerima_snapshot ?? 'Pembeli' }}</td>
                            <td class="px-4 py-3">{{ $pesanan?->tanggal_pesanan?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div>{{ strtoupper($item->kurir_kode ?? '-') }}</div>
                                <div class="text-xs text-slate-500">{{ $item->kurir_layanan ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $kelasStatus }}">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('kelola.pesanan.show', $item->id_sub_pesanan) }}"
                                   class="font-semibold text-[#3E5D3C] hover:underline">Lihat Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $subPesanan->links() }}</div>
    @endif
</div>
@endsection
