
@extends('layouts.pelanggan')

@section('title', 'Checkout')

@section('content')

@php
    $formatRupiah = fn ($angka) => 'Rp' . number_format($angka, 0, ',', '.');
@endphp

<div class="w-full max-w-5xl mx-auto pb-12">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('keranjang.index') }}"
           class="text-sm text-slate-500 hover:text-green-800">
            ← Kembali ke Keranjang
        </a>

        <h1 class="text-3xl font-bold text-slate-800 mt-4">
            Checkout
        </h1>

        <p class="text-slate-500 mt-2">
            Periksa alamat dan pesananmu sebelum melakukan konfirmasi.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf

        @foreach($selectedItemIds as $id)
            <input
                type="hidden"
                name="selected_items[]"
                value="{{ $id }}"
            >
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bagian kiri --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Alamat pengiriman --}}
                <section class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">

                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-800">
                            <span class="text-xl">⌖</span>
                        </div>

                        <div>
                            <h2 class="font-bold text-lg text-slate-800">
                                Alamat Pengiriman
                            </h2>
                            <p class="text-sm text-slate-500">
                                Pilih alamat tujuan pesananmu.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($alamat as $item)
                            <label class="flex gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-green-700 transition">

                                <input
                                    type="radio"
                                    name="id_alamat"
                                    value="{{ $item->id_alamat }}"
                                    class="mt-1 accent-green-800"
                                    {{ old('id_alamat') == $item->id_alamat || (!old('id_alamat') && $loop->first) ? 'checked' : '' }}
                                    required
                                >

                                <div class="flex-1">
                                    <div class="flex flex-wrap justify-between gap-2">
                                        <strong class="text-slate-800">
                                            {{ $item->nama_penerima }}
                                        </strong>

                                        @if($loop->first)
                                            <span class="text-xs rounded-full bg-green-50 text-green-800 px-3 py-1">
                                                Alamat utama
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-slate-600 mt-1">
                                        {{ $item->nomor_telepon }}
                                    </p>

                                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                        {{ $item->alamat_lengkap }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Kode Pos: {{ $item->kode_pos }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Pilihan ongkos kirim --}}
                <section class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">

                    <h2 class="font-bold text-lg text-slate-800 mb-2">
                        Pengiriman
                    </h2>

                    <p class="text-sm text-slate-500 mb-5">
                        Hitung ongkos kirim berdasarkan lokasi UMKM dan alamat tujuan.
                    </p>

                    <button
                        type="button"
                        id="btnHitungOngkir"
                        class="bg-green-800 hover:bg-green-900 text-white font-semibold rounded-xl py-3 px-5 transition"
                    >
                        Hitung Ongkos Kirim
                    </button>

                    <div
                        id="statusOngkir"
                        class="text-sm text-slate-500 mt-3"
                        aria-live="polite"
                    ></div>

                    <div id="hasilOngkir" class="mt-5 space-y-5"></div>
                </section>

                {{-- Metode pembayaran --}}
                <section class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">

                    <h2 class="font-bold text-lg text-slate-800 mb-2">
                        Metode Pembayaran
                    </h2>

                    <p class="text-sm text-slate-500 mb-5">
                        Pilih metode pembayaran untuk seluruh pesananmu.
                    </p>

                    <div class="space-y-3">

                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-green-700 transition">
                            <input
                                type="radio"
                                name="metode_pembayaran"
                                value="COD"
                                class="mt-1 accent-green-800"
                                {{ old('metode_pembayaran', 'COD') === 'COD' ? 'checked' : '' }}
                                required
                            >

                            <div>
                                <strong class="text-slate-800">
                                    Cash on Delivery (COD)
                                </strong>

                                <p class="text-sm text-slate-500 mt-1">
                                    Bayar secara langsung ketika pesanan diterima.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-green-700 transition">
                            <input
                                type="radio"
                                name="metode_pembayaran"
                                value="MIDTRANS"
                                class="mt-1 accent-green-800"
                                {{ old('metode_pembayaran') === 'MIDTRANS' ? 'checked' : '' }}
                                required
                            >

                            <div>
                                <strong class="text-slate-800">
                                    Midtrans
                                </strong>

                                <p class="text-sm text-slate-500 mt-1">
                                    Lakukan pembayaran secara online melalui Midtrans.
                                </p>
                            </div>
                        </label>

                    </div>
                </section>

                {{-- Ringkasan produk --}}
                <section class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">

                    <div class="mb-6">
                        <h2 class="font-bold text-lg text-slate-800">
                            Ringkasan Pesanan
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Produk yang akan kamu pesan.
                        </p>
                    </div>

                    @foreach($kelompokUmkm as $daftarItem)
                        @php
                            $umkm = $daftarItem->first()->produk->umkm;
                            $subtotalUmkm = 0;
                        @endphp

                        <div class="mb-6 last:mb-0">

                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="text-green-800">▣</span>
                                <strong class="text-slate-800">
                                    {{ $umkm->nama_umkm ?? $umkm->nama ?? 'UMKM' }}
                                </strong>
                            </div>

                            <div class="space-y-4">
                                @foreach($daftarItem as $item)
                                    @php
                                        $custom = $item->permintaanCustom;
                                        $isCustom = $custom !== null;

                                        $harga = $isCustom
                                            ? $custom->harga_custom
                                            : $item->produk->harga_retail;

                                        $subtotal = $harga * $item->jumlah;
                                        $subtotalUmkm += $subtotal;
                                    @endphp

                                    <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 rounded-xl bg-amber-50/60 border border-amber-100">

                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-semibold text-slate-800">
                                                    {{ $item->produk->nama }}
                                                </h3>

                                                <span class="text-xs px-2 py-1 rounded-full
                                                    {{ $isCustom ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-800' }}">
                                                    {{ $isCustom ? 'CUSTOM' : 'REGULER' }}
                                                </span>
                                            </div>

                                            @if($isCustom)
                                                <div class="mt-3 text-sm text-slate-600 space-y-1">
                                                    <p>
                                                        <strong>Spesifikasi:</strong>
                                                        {{ $custom->spesifikasi }}
                                                    </p>

                                                    <p>
                                                        <strong>Jenis:</strong>
                                                        {{ $custom->jenis_pesanan === 'CUSTOM_DARI_STOK' ? 'Custom dari Stok' : 'Custom Langsung' }}
                                                    </p>
                                                </div>
                                            @endif

                                            <div class="mt-3 text-sm text-slate-500">
                                                {{ $formatRupiah($harga) }} × {{ $item->jumlah }} unit
                                            </div>
                                        </div>

                                        <div class="sm:text-right flex sm:block justify-between items-center">
                                            <span class="text-xs text-slate-400 sm:block mb-1">
                                                Subtotal
                                            </span>

                                            <strong class="text-green-800">
                                                {{ $formatRupiah($subtotal) }}
                                            </strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between mt-4 text-sm">
                                <span class="text-slate-500">
                                    Subtotal UMKM
                                </span>

                                <strong class="text-slate-800">
                                    {{ $formatRupiah($subtotalUmkm) }}
                                </strong>
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>

            {{-- Bagian kanan --}}
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6 lg:sticky lg:top-24">

                    <h2 class="font-bold text-lg text-slate-800 mb-5">
                        Ringkasan Pembayaran
                    </h2>

                    <div class="space-y-3 text-sm">

                        <div class="flex justify-between text-slate-600">
                            <span>Total produk</span>
                            <span>{{ $keranjang->item->sum('jumlah') }} unit</span>
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal belanja</span>
                            <span>{{ $formatRupiah($totalBelanja) }}</span>
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span>Ongkos kirim</span>
                            <span id="totalOngkir">Belum dihitung</span>
                        </div>

                    </div>

                    <div class="border-t border-dashed border-slate-200 my-5"></div>

                    <div class="flex justify-between items-center gap-3">
                        <strong class="text-slate-800">
                            Total Belanja
                        </strong>

                        <strong
                            id="totalPembayaran"
                            class="text-xl text-green-800"
                            data-subtotal="{{ $totalBelanja }}"
                        >
                            {{ $formatRupiah($totalBelanja) }}
                        </strong>
                    </div>

                    <p
                        id="keteranganTotal"
                        class="text-xs text-slate-400 mt-3"
                    >
                        Total belum termasuk biaya pengiriman.
                    </p>

                    <button
                        type="submit"
                        class="w-full mt-6 bg-green-800 hover:bg-green-900 text-white font-semibold rounded-xl py-3 px-4 transition"
                    >
                        Konfirmasi Pesanan
                    </button>

                    <p class="text-center text-xs text-slate-400 mt-4">
                        Pastikan alamat dan detail pesanan sudah benar.
                    </p>
                </div>
            </aside>

        </div>
    </form>
</div>

{{-- JavaScript Perhitungan Ongkir RajaOngkir --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tombol = document.getElementById('btnHitungOngkir');
    const status = document.getElementById('statusOngkir');
    const hasil = document.getElementById('hasilOngkir');

    const totalOngkir = document.getElementById('totalOngkir');
    const totalPembayaran = document.getElementById('totalPembayaran');
    const keteranganTotal = document.getElementById('keteranganTotal');

    const form = document.querySelector('form[action="{{ route('checkout.store') }}"]');

    const formatRupiah = angka =>
        'Rp' + Number(angka).toLocaleString('id-ID');

    let ongkirTerpilih = {};

    // Hitung ongkir dari RajaOngkir.
    tombol.addEventListener('click', async function () {

        const alamat = document.querySelector('input[name="id_alamat"]:checked');

        if (!alamat) {
            status.textContent = 'Silakan pilih alamat pengiriman terlebih dahulu.';
            return;
        }

        const selectedItems = Array.from(
            form.querySelectorAll('input[name="selected_items[]"]')
        ).map(input => input.value);

        tombol.disabled = true;
        tombol.textContent = 'Menghitung...';

        status.textContent = 'Sedang menghubungi RajaOngkir...';

        hasil.innerHTML = '';
        ongkirTerpilih = {};

        totalOngkir.textContent = 'Belum dipilih';

        totalPembayaran.textContent = formatRupiah(
            totalPembayaran.dataset.subtotal
        );

        keteranganTotal.textContent =
            'Pilih layanan pengiriman untuk menghitung total.';

        form.querySelectorAll('.input-ongkir').forEach(input => input.remove());

        try {

            const response = await fetch("{{ route('checkout.ongkir') }}", {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

                body: JSON.stringify({
                    id_alamat: alamat.value,
                    selected_items: selectedItems
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal menghitung ongkir.');
            }

            if (!data.data || data.data.length === 0) {
                throw new Error('Layanan pengiriman tidak ditemukan.');
            }

            data.data.forEach(umkm => {

                const container = document.createElement('div');

                container.className =
                    'border border-slate-200 rounded-xl p-4';

                const judul = document.createElement('h3');

                judul.className =
                    'font-semibold text-slate-800 mb-2';

                judul.textContent = umkm.nama_umkm;

                const infoBerat = document.createElement('p');

                infoBerat.className =
                    'text-xs text-slate-500 mb-3';

                infoBerat.textContent =
                    'Berat: ' + umkm.berat_gram + ' gram';

                container.append(judul, infoBerat);

                if (!umkm.layanan || umkm.layanan.length === 0) {

                    const pesan = document.createElement('p');

                    pesan.className = 'text-sm text-red-600';

                    pesan.textContent =
                        'Tidak ada layanan pengiriman untuk wilayah ini.';

                    container.appendChild(pesan);

                } else {

                    umkm.layanan.forEach((layanan, index) => {

                        const label = document.createElement('label');

                        label.className =
                            'flex items-start gap-3 p-3 mb-2 rounded-lg border border-slate-200 cursor-pointer hover:border-green-700';

                        const radio = document.createElement('input');

                        radio.type = 'radio';
                        radio.name = 'ongkir_' + umkm.id_umkm;
                        radio.value = index;
                        radio.className = 'mt-1 accent-green-800';
                        radio.required = true;

                        const detail = document.createElement('div');

                        detail.className = 'flex-1 text-sm';

                        const nama = document.createElement('strong');

                        nama.className = 'block text-slate-800';

                        nama.textContent =
                            layanan.nama_kurir + ' - ' + layanan.layanan;

                        const deskripsi = document.createElement('p');

                        deskripsi.className = 'text-slate-500 mt-1';

                        deskripsi.textContent = layanan.deskripsi;

                        const biaya = document.createElement('p');

                        biaya.className =
                            'font-semibold text-green-800 mt-2';

                        biaya.textContent = formatRupiah(layanan.biaya);

                        const estimasi = document.createElement('p');

                        estimasi.className =
                            'text-xs text-slate-400 mt-1';

                        estimasi.textContent = layanan.estimasi
                            ? 'Estimasi: ' + layanan.estimasi
                            : 'Estimasi tidak tersedia';

                        detail.append(nama, deskripsi, biaya, estimasi);

                        label.append(radio, detail);

                        container.appendChild(label);

                        radio.addEventListener('change', function () {

                            ongkirTerpilih[umkm.id_umkm] = layanan;

                            perbaruiTotal();

                        });

                    });
                }

                hasil.appendChild(container);

            });

            status.textContent =
                'Berhasil mendapatkan pilihan pengiriman. Silakan pilih layanan untuk setiap UMKM.';

        } catch (error) {

            status.textContent = error.message;

        } finally {

            tombol.disabled = false;
            tombol.textContent = 'Hitung Ongkos Kirim';

        }

    });


    // Perbarui total setelah layanan dipilih.
    function perbaruiTotal() {

        const jumlahUmkm =
            hasil.querySelectorAll(':scope > div').length;

        const jumlahPilihan =
            Object.keys(ongkirTerpilih).length;

        const total = Object.values(ongkirTerpilih).reduce(
            (sum, layanan) => sum + Number(layanan.biaya),
            0
        );

        totalOngkir.textContent = formatRupiah(total);

        const subtotal =
            Number(totalPembayaran.dataset.subtotal);

        totalPembayaran.textContent =
            formatRupiah(subtotal + total);

        if (jumlahPilihan === jumlahUmkm) {

            keteranganTotal.textContent =
                'Ongkos kirim seluruh UMKM sudah dipilih.';

        } else {

            keteranganTotal.textContent =
                'Pilih layanan pengiriman untuk seluruh UMKM (' +
                jumlahPilihan + '/' + jumlahUmkm + ').';

        }

        // Siapkan data pilihan ongkir untuk tahap penyimpanan pesanan.
        form.querySelectorAll('.input-ongkir').forEach(
            input => input.remove()
        );

        Object.entries(ongkirTerpilih).forEach(
            ([idUmkm, layanan]) => {

                const fields = {
                    kode_kurir: layanan.kode_kurir,
                    nama_kurir: layanan.nama_kurir,
                    layanan: layanan.layanan,
                    biaya: layanan.biaya
                };

                Object.entries(fields).forEach(([field, value]) => {

                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.className = 'input-ongkir';

                    input.name =
                        `pengiriman[${idUmkm}][${field}]`;

                    input.value = value;

                    form.appendChild(input);

                });

            }
        );

    }

});
</script>

@endsection
