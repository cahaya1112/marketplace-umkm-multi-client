@extends('catalog::kelola.layout')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $status = $subPesanan->status_sub_pesanan;
    $warnaStatus = [
        'MENUNGGU_PEMBAYARAN' => 'bg-amber-100 text-amber-800',
        'DIPROSES' => 'bg-blue-100 text-blue-800',
        'DIKIRIM' => 'bg-violet-100 text-violet-800',
        'SELESAI' => 'bg-emerald-100 text-emerald-800',
        'DIBATALKAN' => 'bg-red-100 text-red-700',
    ];
    $kelasStatus = $warnaStatus[$status] ?? 'bg-slate-100 text-slate-700';
    $pesanan = $subPesanan->pesanan;
    $pembayaran = $pesanan?->pembayaran;
@endphp

<div class="w-full">
    <h1 class="text-3xl font-bold">Detail Pesanan</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">
        Pesanan {{ $pesanan?->kode_pesanan ?? '#' . $subPesanan->id_pesanan }} untuk UMKM {{ $umkm->nama }}.
    </p>

    @include('catalog::kelola.partials.menu')

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('kelola.pesanan.index') }}"
           class="rounded-xl border border-[#D2A874]/60 bg-white px-4 py-2 text-sm font-medium hover:bg-[#E3D9BF]/30">
            ← Kembali ke Pesanan
        </a>
        <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $kelasStatus }}">
            {{ str_replace('_', ' ', $status) }}
        </span>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Informasi Pesanan</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Kode pesanan</dt>
                    <dd class="text-right font-semibold">{{ $pesanan?->kode_pesanan ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Subpesanan</dt>
                    <dd class="text-right font-semibold">#{{ $subPesanan->id_sub_pesanan }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Tanggal pesanan</dt>
                    <dd class="text-right">{{ $pesanan?->tanggal_pesanan?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Nama penerima</dt>
                    <dd class="text-right">{{ $pesanan?->nama_penerima_snapshot ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Nomor telepon</dt>
                    <dd class="text-right">{{ $pesanan?->nomor_telepon_snapshot ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Alamat pengiriman</dt>
                    <dd>{{ $pesanan?->alamat_lengkap_snapshot ?? '-' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Informasi Pengiriman</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Kurir</dt>
                    <dd class="text-right font-semibold">{{ strtoupper($subPesanan->kurir_kode ?? '-') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Layanan</dt>
                    <dd class="text-right">{{ $subPesanan->kurir_layanan ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Estimasi</dt>
                    <dd class="text-right">{{ $subPesanan->estimasi_pengiriman ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Biaya pengiriman</dt>
                    <dd class="text-right font-semibold">Rp{{ number_format((int) $subPesanan->biaya_pengiriman, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Nomor resi</dt>
                    <dd class="text-right font-semibold">{{ $subPesanan->nomor_resi ?: 'Belum tersedia' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Informasi Pembayaran</h2>
            @if ($pembayaran)
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Metode</dt>
                        <dd class="text-right font-semibold">{{ $pembayaran->metode_pembayaran }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Status pembayaran</dt>
                        <dd class="text-right font-semibold">{{ $pembayaran->status_pembayaran }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Jumlah bayar</dt>
                        <dd class="text-right font-semibold">Rp{{ number_format((int) $pembayaran->jumlah_bayar, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-slate-500">Informasi pembayaran belum tersedia.</p>
            @endif
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-2 text-lg font-bold">Perbarui Status Pesanan</h2>

            @if ($status === 'DIPROSES')
                <p class="mb-4 text-sm text-slate-600">Masukkan nomor resi untuk mengubah status pesanan menjadi DIKIRIM.</p>
                <form method="POST" action="{{ route('kelola.pesanan.update-status', $subPesanan->id_sub_pesanan) }}"
                      onsubmit="return confirm('Simpan nomor resi dan ubah status menjadi DIKIRIM?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_sub_pesanan" value="DIKIRIM">
                    <label for="nomor_resi" class="mb-2 block text-sm font-medium">Nomor resi</label>
                    <input id="nomor_resi" name="nomor_resi" type="text" required maxlength="255"
                           value="{{ old('nomor_resi', $subPesanan->nomor_resi) }}"
                           placeholder="Masukkan nomor resi"
                           class="mb-4 w-full rounded-xl border border-[#D2A874]/60 px-4 py-2 text-sm focus:border-[#3E5D3C] focus:outline-none">
                    <button type="submit" class="rounded-xl bg-[#3E5D3C] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#314A30]">
                        Simpan Resi & Kirim Pesanan
                    </button>
                </form>
            @elseif ($status === 'DIKIRIM')
                <p class="mb-4 text-sm text-slate-600">Setelah pesanan diterima, tandai pesanan sebagai SELESAI.</p>
                <form method="POST" action="{{ route('kelola.pesanan.update-status', $subPesanan->id_sub_pesanan) }}"
                      onsubmit="return confirm('Tandai pesanan ini sebagai SELESAI?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_sub_pesanan" value="SELESAI">
                    <button type="submit" class="rounded-xl bg-[#3E5D3C] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#314A30]">
                        Tandai Selesai
                    </button>
                </form>
            @elseif ($status === 'MENUNGGU_PEMBAYARAN')
                <p class="text-sm text-amber-700">Pesanan masih menunggu pembayaran. Status ini diperbarui melalui proses pembayaran.</p>
            @elseif ($status === 'SELESAI')
                <p class="text-sm text-emerald-700">Pesanan ini sudah selesai.</p>
            @elseif ($status === 'DIBATALKAN')
                <p class="text-sm text-red-700">Pesanan ini telah dibatalkan.</p>
            @else
                <p class="text-sm text-slate-500">Tidak ada tindakan yang tersedia untuk status ini.</p>
            @endif
        </section>
    </div>
</div>
@endsection
