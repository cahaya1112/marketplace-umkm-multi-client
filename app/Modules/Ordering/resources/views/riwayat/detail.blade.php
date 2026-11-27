
@extends('layouts.pelanggan')

@section('title', 'Detail Pesanan | Marketplace UMKM')

@section('content')
<a href="{{ url('/pesanan-saya') }}"
   class="text-sm text-forest hover:underline">
    ← Kembali ke Pesanan Saya
</a>

    <h1 class="mt-3 text-2xl font-bold text-forest">
        Detail Pesanan
    </h1>

    <p class="mt-1 text-sm text-gray-500">
        Informasi lengkap mengenai pesananmu.
    </p>
</div>

<div class="space-y-5">

    {{-- INFORMASI PESANAN --}}
    <div class="rounded-xl border border-[#E8DFC6] bg-white p-5 shadow-sm">
        <h2 class="mb-4 font-semibold text-ink">
            Informasi Pesanan
        </h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-xs text-gray-400">Kode Pesanan</p>
                <p class="font-semibold text-forest">
                    {{ $pesanan->kode_pesanan }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">Tanggal Pesanan</p>
                <p class="text-sm text-gray-700">
                    {{ $pesanan->tanggal_pesanan?->format('d M Y, H:i') ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- INFORMASI PENERIMA --}}
    <div class="rounded-xl border border-[#E8DFC6] bg-white p-5 shadow-sm">
        <h2 class="mb-4 font-semibold text-ink">
            Informasi Penerima
        </h2>

        <div class="space-y-2 text-sm">
            <div>
                <p class="text-xs text-gray-400">Nama Penerima</p>
                <p class="text-gray-700">
                    {{ $pesanan->nama_penerima_snapshot ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">Nomor Telepon</p>
                <p class="text-gray-700">
                    {{ $pesanan->nomor_telepon_snapshot ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">Alamat Pengiriman</p>
                <p class="text-gray-700">
                    {{ $pesanan->alamat_lengkap_snapshot ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">Kode Pos</p>
                <p class="text-gray-700">
                    {{ $pesanan->kode_pos_snapshot ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- DETAIL PRODUK PER UMKM --}}
    <div class="rounded-xl border border-[#E8DFC6] bg-white p-5 shadow-sm">
        <h2 class="mb-4 font-semibold text-ink">
            Rincian Produk
        </h2>

        <div class="space-y-5">
            @foreach ($pesanan->subPesanan as $sub)
                <div class="rounded-lg border border-gray-100 p-4">
                    <div class="mb-3 flex flex-wrap justify-between gap-2">
                        <div>
                            <p class="text-xs text-gray-400">Penjual</p>
                            <p class="font-semibold text-forest">
                                {{ $sub->umkm->nama_umkm ?? 'UMKM' }}
                            </p>
                        </div>

                        <span class="h-fit rounded-full bg-[#FAEDCD] px-3 py-1 text-xs font-medium text-forest">
                            {{ str_replace('_', ' ', $sub->status_sub_pesanan) }}
                        </span>
                    </div>

                    @foreach ($sub->detail as $detail)
                        <div class="flex justify-between gap-3 border-t border-gray-100 py-3 text-sm">
                            <div>
                                <p class="font-medium text-gray-700">
                                    {{ $detail->nama_produk_snapshot ?? 'Produk' }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $detail->jumlah }} produk
                                </p>
                            </div>

                            <p class="whitespace-nowrap font-medium text-gray-700">
                                Rp{{ number_format(
                                    (($detail->harga_custom ?? 0) > 0
                                        ? $detail->harga_custom
                                        : $detail->harga_retail) * $detail->jumlah,
                                    0, ',', '.'
                                ) }}
                            </p>
                        </div>
                    @endforeach

                    {{-- INFORMASI PENGIRIMAN --}}
                    <div class="mt-3 border-t border-dashed border-gray-200 pt-3">
                        <h3 class="mb-2 text-sm font-semibold text-ink">
                            Informasi Pengiriman
                        </h3>

                        <div class="grid gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-400">Kurir</p>
                                <p class="text-gray-700">
                                    {{ strtoupper($sub->kurir_kode ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Layanan</p>
                                <p class="text-gray-700">
                                    {{ $sub->kurir_layanan ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Estimasi</p>
                                <p class="text-gray-700">
                                    {{ $sub->estimasi_pengiriman ?: '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Nomor Resi</p>
                                <p class="text-gray-700">
                                    {{ $sub->nomor_resi ?: 'Belum tersedia' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-500">Biaya Pengiriman</span>
                            <span class="font-medium text-gray-700">
                                Rp{{ number_format($sub->biaya_pengiriman ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- INFORMASI PEMBAYARAN --}}
    <div class="rounded-xl border border-[#E8DFC6] bg-white p-5 shadow-sm">
        <h2 class="mb-4 font-semibold text-ink">
            Informasi Pembayaran
        </h2>

        @if ($pesanan->pembayaran)
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-gray-400">Metode Pembayaran</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ strtoupper($pesanan->pembayaran->metode_pembayaran) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Status Pembayaran</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ $pesanan->pembayaran->status_pembayaran }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Jumlah Pembayaran</p>
                    <p class="text-sm font-semibold text-forest">
                        Rp{{ number_format($pesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500">
                Informasi pembayaran belum tersedia.
            </p>
        @endif
    </div>

</div>
@endsection
