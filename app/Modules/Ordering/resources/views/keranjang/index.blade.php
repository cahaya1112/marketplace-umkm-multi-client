
@extends('layouts.pelanggan')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">
            Keranjang Belanja
        </h1>
        <p class="mt-2 text-slate-600">
            Periksa kembali produk pilihanmu sebelum checkout.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-100 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('galat'))
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-700">
            {{ session('galat') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($keranjang->item->isEmpty())

        <div class="rounded-2xl border border-[#e2d9bd] bg-white p-12 text-center">
            <h2 class="text-xl font-semibold text-slate-800">
                Keranjang masih kosong
            </h2>

            <p class="mt-2 text-slate-600">
                Yuk, temukan produk UMKM favoritmu!
            </p>

            <a href="{{ route('katalog.index') }}"
               class="mt-6 inline-block rounded-xl bg-[#3d5a3d] px-6 py-3 font-semibold text-white hover:opacity-90">
                Mulai Belanja
            </a>
        </div>

    @else

        @php
            $totalKeranjang = 0;
        @endphp

        <div class="space-y-6">

            @foreach ($kelompokUmkm as $idUmkm => $items)

                @php
                    $namaUmkm = $items->first()->produk->umkm->nama ?? 'UMKM';
                    $subtotalUmkm = 0;
                @endphp

                <section class="overflow-hidden rounded-2xl border border-[#e2d9bd] bg-white shadow-sm">

                    <div class="border-b border-[#e2d9bd] bg-[#f8ecd0] px-6 py-4">
                        <h2 class="font-bold text-[#3d5a3d]">
                            {{ $namaUmkm }}
                        </h2>
                    </div>

                    <div class="divide-y divide-[#e2d9bd]">

                        @foreach ($items as $item)

                            @php
                                $produk = $item->produk;
                                $custom = $item->permintaanCustom;

                                $isCustom = $custom !== null;

                                $hargaSatuan = $isCustom
                                    ? $custom->harga_custom
                                    : $produk->harga_retail;

                                $subtotal = $hargaSatuan * $item->jumlah;

                                $subtotalUmkm += $subtotal;
                            @endphp

                            <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center">

                                {{-- Gambar produk --}}
                                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-[#f8ecd0]">

                                    @if ($produk->gambar_url)
                                        <img src="{{ $produk->gambar_url }}"
                                             alt="{{ $produk->nama }}"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                            Tanpa gambar
                                        </div>
                                    @endif

                                </div>

                                {{-- Informasi produk --}}
                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="font-semibold text-slate-900">
                                            {{ $produk->nama }}
                                        </h3>

                                        @if ($isCustom)
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                                CUSTOM
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                REGULER
                                            </span>
                                        @endif

                                    </div>

                                    <p class="mt-1 text-sm font-medium text-[#3d5a3d]">
                                        Rp{{ number_format($hargaSatuan, 0, ',', '.') }}
                                        / unit
                                    </p>

                                    @if ($isCustom)

                                        <div class="mt-3 rounded-lg border border-purple-200 bg-purple-50 p-3">

                                            <p class="text-xs font-semibold text-purple-800">
                                                Spesifikasi Custom
                                            </p>

                                            <p class="mt-1 whitespace-pre-line text-sm text-purple-700">
                                                {{ $custom->spesifikasi }}
                                            </p>

                                            @if ($custom->catatan_umkm)
                                                <p class="mt-2 text-xs text-purple-700">
                                                    Catatan UMKM:
                                                    {{ $custom->catatan_umkm }}
                                                </p>
                                            @endif

                                        </div>

                                    @else

                                        <p class="mt-1 text-xs text-slate-500">
                                            Stok tersedia:
                                            {{ $produk->stok->jumlah_stok ?? 0 }}
                                        </p>

                                    @endif

                                    {{-- Pengaturan jumlah --}}
                                    <div class="mt-3 flex flex-wrap items-center gap-3">

                                        @if ($isCustom)

                                            <span class="text-sm text-slate-600">
                                                Jumlah: {{ $item->jumlah }} unit
                                            </span>

                                            <span class="text-xs text-purple-600">
                                                Jumlah custom ditetapkan 1 unit.
                                            </span>

                                        @else

                                            <form method="POST"
                                                  action="{{ route('keranjang.update', $item->id_keranjang_item) }}"
                                                  class="flex items-center gap-2">

                                                @csrf
                                                @method('PATCH')

                                                <label class="text-sm text-slate-600">
                                                    Jumlah
                                                </label>

                                                <input type="number"
                                                       name="jumlah"
                                                       value="{{ $item->jumlah }}"
                                                       min="1"
                                                       max="{{ $produk->stok->jumlah_stok ?? 0 }}"
                                                       required
                                                       class="w-20 rounded-lg border border-[#c9a26c]/60 px-3 py-2">

                                                <button type="submit"
                                                        class="text-sm font-semibold text-[#3d5a3d] hover:underline">
                                                    Perbarui
                                                </button>

                                            </form>

                                        @endif

                                        {{-- Hapus item --}}
                                        <form method="POST"
                                              action="{{ route('keranjang.destroy', $item->id_keranjang_item) }}"
                                              onsubmit="return confirm('Hapus produk ini dari keranjang?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-sm font-semibold text-red-600 hover:underline">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </div>

                                {{-- Subtotal item --}}
                                <div class="text-left sm:text-right">

                                    <p class="text-xs text-slate-500">
                                        Subtotal
                                    </p>

                                    <p class="font-bold text-[#3d5a3d]">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    {{-- Subtotal UMKM --}}
                    <div class="flex justify-between border-t border-[#e2d9bd] bg-[#fffdf8] px-6 py-4">

                        <span class="font-semibold text-slate-700">
                            Subtotal toko
                        </span>

                        <span class="font-bold text-[#3d5a3d]">
                            Rp{{ number_format($subtotalUmkm, 0, ',', '.') }}
                        </span>

                    </div>

                </section>

                @php
                    $totalKeranjang += $subtotalUmkm;
                @endphp

            @endforeach

        </div>

        {{-- Total keranjang --}}
        <div class="mt-8 rounded-2xl border border-[#e2d9bd] bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between gap-4">

                <span class="text-lg font-semibold text-slate-700">
                    Total Keranjang
                </span>

                <span class="text-2xl font-bold text-[#3d5a3d]">
                    Rp{{ number_format($totalKeranjang, 0, ',', '.') }}
                </span>

            </div>

            <p class="mt-2 text-sm text-slate-500">
                Ongkos kirim dihitung saat proses checkout.
            </p>

            <div class="mt-6 flex justify-end">

                <button type="button"
                        disabled
                        title="Fitur checkout akan dikerjakan pada tahap berikutnya"
                        class="cursor-not-allowed rounded-xl bg-[#3d5a3d] px-8 py-3 font-semibold text-white opacity-50">
                    Lanjut Checkout
                </button>

            </div>

        </div>

    @endif

</div>
@endsection
