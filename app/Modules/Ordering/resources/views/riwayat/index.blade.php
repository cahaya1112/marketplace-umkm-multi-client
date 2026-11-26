
@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya | Marketplace UMKM')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-forest">Pesanan Saya</h1>
    <p class="mt-1 text-sm text-gray-500">
        Lihat daftar transaksi dan perkembangan pesananmu.
    </p>
</div>

@if ($pesanan->isEmpty())
    <div class="rounded-2xl border border-[#E8DFC6] bg-white p-10 text-center shadow-sm">
        <div class="mb-3 text-4xl">📦</div>
        <h2 class="font-semibold text-ink">Belum Ada Pesanan</h2>
        <p class="mt-2 text-sm text-gray-500">
            Kamu belum memiliki pesanan. Yuk, temukan produk UMKM favoritmu!
        </p>
        <a href="{{ route('katalog.index') }}"
           class="mt-5 inline-block rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white hover:bg-forestdark">
            Lihat Katalog
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach ($pesanan as $item)
            <div class="rounded-xl border border-[#E8DFC6] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-3">
                    <div>
                        <p class="text-xs text-gray-400">Kode Pesanan</p>
                        <p class="font-semibold text-forest">
                            {{ $item->kode_pesanan }}
                        </p>
                    </div>

                    <p class="text-xs text-gray-500">
                        {{ $item->tanggal_pesanan?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>

                <div class="space-y-4 py-4">
                    @foreach ($item->subPesanan as $sub)
                        <div>
                            <p class="mb-2 text-sm font-semibold text-ink">
                                {{ $sub->umkm->nama_umkm ?? 'UMKM' }}
                            </p>

                            @foreach ($sub->detail as $detail)
                                <div class="flex justify-between gap-3 py-1 text-sm">
                                    <div>
                                        <p class="text-gray-700">
                                            {{ $detail->nama_produk_snapshot ?? 'Produk' }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $detail->jumlah }} produk
                                        </p>
                                    </div>

                                    <p class="whitespace-nowrap font-medium">
                                        Rp{{ number_format(
                                            (($detail->harga_custom ?? 0) > 0
                                                ? $detail->harga_custom
                                                : $detail->harga_retail) * $detail->jumlah,
                                            0, ',', '.'
                                        ) }}
                                    </p>
                                </div>
                            @endforeach

                            <p class="mt-2 text-xs text-gray-500">
                                Status: {{ str_replace('_', ' ', $sub->status_sub_pesanan) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-3">
                    <a href="{{ route('pesanan.detail', $item->id_pesanan) }}"
                       class="rounded-lg bg-forest px-4 py-2 text-xs font-medium text-white hover:bg-forestdark">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $pesanan->links() }}
    </div>
@endif
@endsection
