@extends('catalog::kelola.layout')

@section('title', 'Custom Pesanan')

@section('content')
<div class="mx-auto max-w-full px-4 py-8">
    <h1 class="text-2xl font-bold text-[#232934]">Custom Pesanan</h1>
    <p class="mb-6 mt-1 text-sm text-slate-600">Permintaan custom untuk produk {{ $umkm->nama }}</p>

    @include('catalog::kelola.partials.menu')

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach ([null => 'Semua', 'PENDING' => 'Menunggu', 'DISETUJUI' => 'Disetujui', 'DITOLAK' => 'Ditolak'] as $nilai => $label)
            <a href="{{ route('kelola.custom.index', $nilai ? ['status' => $nilai] : []) }}"
               class="rounded-lg px-4 py-1.5 font-medium {{ ($filter ?? null) === $nilai ? 'bg-[#3E5D3C] text-white' : 'bg-white hover:bg-[#E3D9BF]/60' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($daftar as $c)
            @php
                $badge = [
                    'PENDING'   => ['Menunggu', 'bg-amber-100 text-amber-700'],
                    'DISETUJUI' => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
                    'DITOLAK'   => ['Ditolak', 'bg-red-100 text-red-700'],
                ][$c->status_custom] ?? [$c->status_custom, 'bg-slate-100 text-slate-700'];
            @endphp
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-[#232934]">{{ $c->produk->nama ?? 'Produk dihapus' }}</p>
                        <p class="text-sm text-slate-500">Pemohon: {{ $c->pembeli->nama ?? '-' }}</p>
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
                        <textarea name="catatan_umkm" rows="2" placeholder="Catatan untuk pembeli (opsional)"
                                  class="w-full rounded-xl border border-[#D2A874]/60 px-4 py-2 text-sm"></textarea>
                        <div class="flex gap-3">
                            <button type="submit" formaction="{{ route('kelola.custom.proses', [$c->id_custom, 'setujui']) }}"
                                    class="rounded-xl bg-[#3E5D3C] px-5 py-2 text-sm font-semibold text-white">Setujui</button>
                            <button type="submit" formaction="{{ route('kelola.custom.proses', [$c->id_custom, 'tolak']) }}"
                                    class="rounded-xl border border-red-300 px-5 py-2 text-sm font-semibold text-red-600">Tolak</button>
                        </div>
                    </form>
                @elseif ($c->catatan_umkm)
                    <p class="mt-3 text-sm text-slate-600"><span class="font-medium">Catatan UMKM:</span> {{ $c->catatan_umkm }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl bg-white p-10 text-center text-slate-500 shadow-sm">Belum ada permintaan custom.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $daftar->links() }}</div>
</div>
@endsection