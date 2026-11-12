@extends('catalog::kelola.layout')

@section('title', 'Custom Pesanan')

@section('content')
@php
    $daftarBadge = [
        'PENDING'              => ['Perlu ditanggapi', 'bg-amber-100 text-amber-700'],
        'MENUNGGU_PERSETUJUAN' => ['Menunggu pembeli', 'bg-sky-100 text-sky-700'],
        'DISETUJUI'            => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
        'DITOLAK'              => ['Ditolak', 'bg-red-100 text-red-700'],
    ];
    $tabFilter = [
        ''                     => 'Semua',
        'PENDING'              => 'Perlu ditanggapi',
        'MENUNGGU_PERSETUJUAN' => 'Menunggu pembeli',
        'DISETUJUI'            => 'Disetujui',
        'DITOLAK'              => 'Ditolak',
    ];
@endphp

<div class="w-full">
    <h1 class="text-3xl font-bold">Custom Pesanan</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">Permintaan custom untuk produk {{ $umkm->nama }}</p>

    @include('catalog::kelola.partials.menu')

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach ($tabFilter as $nilai => $label)
            @php
                $aktif = ($filter ?? '') === (string) $nilai;
            @endphp
            <a href="{{ $nilai === '' ? route('kelola.custom.index') : route('kelola.custom.index', ['status' => $nilai]) }}"
               class="rounded-lg px-4 py-1.5 font-medium {{ $aktif ? 'bg-[#3E5D3C] text-white' : 'bg-white hover:bg-[#E3D9BF]/60' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($daftar->count() === 0)
        <div class="rounded-2xl bg-white p-10 text-center text-slate-500 shadow-sm">Belum ada permintaan custom.</div>
    @else
        <div class="space-y-4">
            @foreach ($daftar as $c)
                @php
                    $badge = $daftarBadge[$c->status_custom] ?? [$c->status_custom, 'bg-slate-100 text-slate-700'];
                    $namaProduk = $c->produk ? $c->produk->nama : 'Produk dihapus';
                    $namaPembeli = $c->pembeli ? $c->pembeli->nama : '-';
                @endphp
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ $namaProduk }}</p>
                            <p class="text-sm text-slate-500">Pemohon: {{ $namaPembeli }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $badge[1] }}">{{ $badge[0] }}</span>
                    </div>

                    <div class="mt-3 rounded-xl bg-[#F9ECCD]/60 p-4 text-sm">
                        <p class="mb-1 text-xs font-semibold uppercase text-slate-500">Spesifikasi custom</p>
                        <p class="whitespace-pre-line">{{ $c->spesifikasi }}</p>
                    </div>

                    @if ($c->status_custom === 'PENDING')
                        <form method="POST" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="mb-1 block text-sm font-medium">Harga penawaran (Rp)</label>
                                <input type="number" min="1" name="harga_custom" required placeholder="Contoh: 150000"
                                       class="w-full max-w-xs rounded-xl border border-[#D2A874]/60 px-4 py-2 text-sm">
                            </div>
                            <textarea name="catatan_umkm" rows="2" placeholder="Catatan untuk pembeli (opsional)"
                                      class="w-full rounded-xl border border-[#D2A874]/60 px-4 py-2 text-sm"></textarea>
                            <div class="flex gap-3">
                                <button type="submit" formaction="{{ route('kelola.custom.proses', [$c->id_custom, 'setujui']) }}"
                                        class="rounded-xl bg-[#3E5D3C] px-5 py-2 text-sm font-semibold text-white">Kirim Penawaran</button>
                                <button type="submit" formnovalidate formaction="{{ route('kelola.custom.proses', [$c->id_custom, 'tolak']) }}"
                                        class="rounded-xl border border-red-300 px-5 py-2 text-sm font-semibold text-red-600">Tolak</button>
                            </div>
                        </form>
                    @else
                        @if ($c->harga_custom !== null)
                            <p class="mt-3 text-sm text-slate-700"><span class="font-medium">Harga penawaran:</span>
                                Rp{{ number_format($c->harga_custom, 0, ',', '.') }}</p>
                        @endif
                        @if ($c->catatan_umkm)
                            <p class="mt-1 text-sm text-slate-600"><span class="font-medium">Catatan UMKM:</span> {{ $c->catatan_umkm }}</p>
                        @endif
                        @if ($c->status_custom === 'MENUNGGU_PERSETUJUAN')
                            <p class="mt-2 text-sm text-sky-700">Menunggu keputusan pembeli.</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $daftar->links() }}</div>
    @endif
</div>
@endsection