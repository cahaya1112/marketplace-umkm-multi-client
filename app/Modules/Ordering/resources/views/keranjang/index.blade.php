
@extends('layouts.pelanggan')

@section('title', 'Keranjang Belanja')

@section('content')

<div class="w-full">
    
    {{-- Judul halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-ink">
            Keranjang Belanja
        </h1>
        <p class="mt-2 text-base text-slate-600">
            Periksa kembali produk pilihanmu sebelum checkout.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-100 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('galat'))
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-sm text-red-700">
            {{ session('galat') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($keranjang->item->isEmpty())

        {{-- Keranjang kosong --}}
        <div class="rounded-2xl border border-[#e2d9bd] bg-white p-12 text-center shadow-sm">

            <div class="mb-4 flex justify-center text-forest">
                <x-gi name="cart" class="h-12 w-12" />
            </div>

            <h2 class="text-xl font-semibold text-ink">
                Keranjang masih kosong
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Yuk, temukan produk UMKM favoritmu!
            </p>

            <a href="{{ route('katalog.index') }}"
               class="mt-6 inline-flex items-center rounded-xl bg-forest px-6 py-3 text-sm font-semibold text-white transition hover:bg-forestdark">
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

                    {{-- Nama UMKM --}}
                    <div class="flex items-center gap-2 border-b border-[#e2d9bd] bg-[#f8ecd0] px-6 py-4">

                        <x-gi name="store" class="h-5 w-5 text-forest" />

                        <h2 class="text-base font-bold text-forest">
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

                            <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start">

                        {{-- Checkbox pilihan checkout --}}
                                <input
                                    type="checkbox"
                                    name="selected_items[]"
                                    value="{{ $item->id_keranjang_item }}"
                                    form="form-pilih-checkout"
                                    data-price="{{ $subtotal }}"
                                    data-qty="{{ $item->jumlah }}"
                                    class="checkout-checkbox h-5 w-5 rounded border-gray-300 text-green-800 focus:ring-green-700 cursor-pointer"
                                    aria-label="Pilih {{ $produk->nama }} untuk checkout"
                                >

                                {{-- Gambar --}}
                                <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-[#f8ecd0]">

                                    @if ($produk->gambar_url)
                                        <img
                                            src="{{ $produk->gambar_url }}"
                                            alt="{{ $produk->nama }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                            Tanpa gambar
                                        </div>
                                    @endif

                                </div>

                                {{-- Informasi produk --}}
                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="text-base font-semibold text-ink">
                                            {{ $produk->nama }}
                                        </h3>

                                        @if ($isCustom)
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-[11px] font-semibold text-purple-700">
                                                CUSTOM
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                                                REGULER
                                            </span>
                                        @endif

                                    </div>

                                    {{-- Deskripsi produk --}}
                                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                        {{ $produk->deskripsi ?: 'Belum ada deskripsi produk.' }}
                                    </p>

                                    {{-- Harga --}}
                                    <p class="mt-2 text-sm font-semibold text-forest">
                                        Rp{{ number_format($hargaSatuan, 0, ',', '.') }}
                                        <span class="font-normal text-slate-500">/ unit</span>
                                    </p>

                                    {{-- Informasi custom --}}
                                    @if ($isCustom)

                                        <div class="mt-3 rounded-xl border border-purple-200 bg-purple-50 p-4">

                                            <div class="flex items-center gap-2">
                                                <x-gi name="tag" class="h-4 w-4 text-purple-700" />

                                                <p class="text-sm font-semibold text-purple-800">
                                                    Spesifikasi Pesanan Custom
                                                </p>
                                            </div>

                                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-purple-700">
                                                {{ $custom->spesifikasi }}
                                            </p>

                                            <div class="mt-3 text-sm text-purple-800">
                                            <p>
                                                <strong>Jenis:</strong>
                                                {{ $custom->jenis_pesanan === 'CUSTOM_DARI_STOK'
                                                    ? 'Custom dari Stok'
                                                    : 'Custom Langsung' }}
                                            </p>

                                            <p>
                                                <strong>Jumlah:</strong>
                                                {{ $item->jumlah }} unit
                                            </p>
                                        </div>

                                            @if ($custom->catatan_umkm)
                                                <div class="mt-3 border-t border-purple-200 pt-3">
                                                    <p class="text-xs font-semibold text-purple-800">
                                                        Catatan dari UMKM
                                                    </p>

                                                    <p class="mt-1 text-sm text-purple-700">
                                                        {{ $custom->catatan_umkm }}
                                                    </p>
                                                </div>
                                            @endif

                                        </div>

                                    @else

                                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                            <x-gi name="package" class="h-4 w-4" />

                                            Stok tersedia:
                                            {{ $produk->stok->jumlah_stok ?? 0 }} unit
                                        </div>

                                    @endif

                                    {{-- Jumlah dan tindakan --}}
                                    <div class="mt-4 flex flex-wrap items-center gap-4">


                                    @if ($isCustom)

                                        <span class="rounded-lg bg-purple-50 px-3 py-2 text-sm text-purple-700">
                                            Jumlah: {{ $item->jumlah }} unit
                                        </span>

                                        <span class="text-xs text-purple-600">
                                            {{ $custom->jenis_pesanan === 'CUSTOM_DARI_STOK'
                                                ? 'Custom menggunakan stok produk reguler.'
                                                : 'Custom langsung tanpa mengambil stok reguler.' }}
                                        </span>

                                    @else


                                            <form
                                                method="POST"
                                                action="{{ route('keranjang.update', $item->id_keranjang_item) }}"
                                                class="flex flex-wrap items-center gap-2"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <label class="text-sm text-slate-600">
                                                    Jumlah
                                                </label>

                                                <input
                                                    type="number"
                                                    name="jumlah"
                                                    value="{{ $item->jumlah }}"
                                                    min="1"
                                                    max="{{ $produk->stok->jumlah_stok ?? 0 }}"
                                                    required
                                                    class="w-20 rounded-lg border border-[#c9a26c]/60 px-3 py-2 text-sm focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest"
                                                >

                                                <button
                                                    type="submit"
                                                    class="text-sm font-semibold text-forest hover:underline"
                                                >
                                                    Perbarui
                                                </button>

                                            </form>

                                        @endif

                                        {{-- Hapus --}}
                                        <form
                                            method="POST"
                                            action="{{ route('keranjang.destroy', $item->id_keranjang_item) }}"
                                            onsubmit="return confirm('Hapus produk ini dari keranjang?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-sm font-semibold text-red-600 hover:underline"
                                            >
                                                Hapus
                                            </button>
                                        </form>

                                    </div>

                                </div>

                                {{-- Subtotal --}}
                                <div class="shrink-0 text-left sm:min-w-32 sm:text-right">

                                    <p class="text-xs text-slate-500">
                                        Subtotal
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-forest">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    {{-- Subtotal UMKM --}}
                    <div class="flex items-center justify-between border-t border-[#e2d9bd] bg-[#fffdf8] px-6 py-4">

                        <span class="text-sm font-semibold text-slate-700">
                            Subtotal toko
                        </span>

                        <span class="font-bold text-forest">
                            Rp{{ number_format($subtotalUmkm, 0, ',', '.') }}
                        </span>

                    </div>

                </section>

                @php
                    $totalKeranjang += $subtotalUmkm;
                @endphp

            @endforeach

        </div>

        
{{-- Ringkasan pemilihan checkout --}}
<div class="mt-8 rounded-2xl border border-[#e2d9bd] bg-white p-6 shadow-sm">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-700">
                Total Seluruh Keranjang
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Seluruh produk yang tersimpan di keranjang.
            </p>
        </div>

        <span class="text-xl font-bold text-slate-700">
            Rp{{ number_format($totalKeranjang, 0, ',', '.') }}
        </span>
    </div>

    <div class="my-5 border-t border-dashed border-slate-200"></div>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-forest">
                Total Item Terpilih
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                <span id="jumlah-item-terpilih">0</span> unit dipilih untuk checkout.
            </p>
        </div>

        <span id="total-item-terpilih"
              class="text-2xl font-bold text-forest">
            Rp0
        </span>
    </div>

    <div class="mt-6 flex justify-end">
        <form
            id="form-pilih-checkout"
            action="{{ route('checkout.index') }}"
            method="GET"
        >
            <button
                id="btn-checkout"
                type="submit"
                disabled
                class="inline-flex items-center gap-2 rounded-xl bg-forest px-6 py-3 text-sm font-semibold text-white transition hover:bg-forestdark disabled:cursor-not-allowed disabled:opacity-40"
            >
                Lanjut Checkout
                <x-gi name="arrow-right" class="h-4 w-4" />
            </button>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.checkout-checkbox');
    const totalElement = document.getElementById('total-item-terpilih');
    const jumlahElement = document.getElementById('jumlah-item-terpilih');
    const tombolCheckout = document.getElementById('btn-checkout');

    function formatRupiah(angka) {
        return 'Rp' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function hitungPilihan() {
        let total = 0;
        let jumlah = 0;
        let dipilih = 0;

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                total += Number(checkbox.dataset.price);
                jumlah += Number(checkbox.dataset.qty);
                dipilih++;
            }
        });

        totalElement.textContent = formatRupiah(total);
        jumlahElement.textContent = jumlah;

        tombolCheckout.disabled = dipilih === 0;
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', hitungPilihan);
    });

    hitungPilihan();
});
</script>

@endif

</div>

@endsection

