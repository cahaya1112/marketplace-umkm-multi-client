
@extends('layouts.pelanggan')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">
            Custom Saya
        </h1>
        <p class="mt-1 text-gray-500">
            Lihat perkembangan permintaan custom dan tanggapi penawaran harga dari UMKM.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('galat'))
        <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-700">
            {{ session('galat') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Daftar Permintaan Custom --}}
    @forelse($daftar as $c)

        <div class="mb-5 rounded-xl border bg-white p-5 shadow-sm">

            {{-- Informasi Produk dan Status --}}
            <div class="mb-4 flex flex-wrap justify-between gap-3">

                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $c->produk->nama_produk ?? $c->produk->nama ?? 'Produk tidak tersedia' }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        UMKM:
                        {{ $c->produk->umkm->nama_umkm ?? $c->produk->umkm->nama ?? 'Tidak diketahui' }}
                    </p>
                </div>

                @php
                    $status = [
                        'PENDING' => [
                            'Menunggu UMKM',
                            'bg-yellow-100 text-yellow-700'
                        ],
                        'MENUNGGU_PERSETUJUAN' => [
                            'Menunggu Persetujuan Kamu',
                            'bg-blue-100 text-blue-700'
                        ],
                        'DISETUJUI' => [
                            'Disetujui',
                            'bg-green-100 text-green-700'
                        ],
                        'DITOLAK' => [
                            'Ditolak',
                            'bg-red-100 text-red-700'
                        ],
                    ];

                    [$label, $warna] = $status[$c->status_custom]
                        ?? ['Status tidak diketahui', 'bg-gray-100 text-gray-700'];
                @endphp

                <span class="h-fit rounded-full px-3 py-1 text-sm font-medium {{ $warna }}">
                    {{ $label }}
                </span>

            </div>

            {{-- Detail Custom --}}
            <div class="space-y-3 text-sm">

                <div>
                    <p class="font-semibold text-gray-700">
                        Spesifikasi Custom
                    </p>

                    <p class="whitespace-pre-line text-gray-600">
                        {{ $c->spesifikasi }}
                    </p>
                </div>

                @if($c->harga_custom !== null)
                    <div>
                        <p class="font-semibold text-gray-700">
                            Penawaran Harga UMKM
                        </p>

                        <p class="text-lg font-bold text-green-700">
                            Rp{{ number_format($c->harga_custom, 0, ',', '.') }}
                        </p>
                    </div>
                @endif

                @if($c->catatan_umkm)
                    <div>
                        <p class="font-semibold text-gray-700">
                            Catatan UMKM
                        </p>

                        <p class="text-gray-600">
                            {{ $c->catatan_umkm }}
                        </p>
                    </div>
                @endif

            </div>

            {{-- Aksi Berdasarkan Status --}}
            @if($c->status_custom === 'MENUNGGU_PERSETUJUAN')

                <div class="mt-5 border-t pt-4">

                    <p class="mb-3 text-sm text-gray-600">
                        UMKM telah memberikan penawaran harga.
                        Silakan pilih untuk menyetujui atau menolak.
                    </p>

                    <div class="flex flex-wrap gap-3">

                        <form method="POST"
                              action="{{ route('custom.keputusan', [$c->id_custom, 'setujui']) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    onclick="return confirm('Yakin ingin menyetujui penawaran harga ini?')"
                                    class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                                Setujui Harga
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('custom.keputusan', [$c->id_custom, 'tolak']) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    onclick="return confirm('Yakin ingin menolak penawaran harga ini?')"
                                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                                Tolak Harga
                            </button>
                        </form>

                    </div>
                </div>

            @elseif($c->status_custom === 'PENDING')

                <p class="mt-5 border-t pt-4 text-sm text-yellow-700">
                    Permintaan sedang ditinjau oleh UMKM.
                    Tunggu sampai UMKM memberikan penawaran harga.
                </p>

            @elseif($c->status_custom === 'DISETUJUI')

                <div class="mt-5 border-t pt-4">

                    <p class="mb-3 text-sm text-green-700">
                        Penawaran telah kamu setujui.
                    </p>

                    @if($c->id_keranjang_item === null)

                        <form method="POST"
                              action="{{ route('custom.keranjang', $c->id_custom) }}">
                            @csrf

                            <button type="submit"
                                    class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                                Masukkan ke Keranjang
                            </button>
                        </form>

                    @else

                        <p class="text-sm text-green-700">
                            Custom ini sudah dimasukkan ke keranjang.
                        </p>

                        <a href="{{ route('keranjang.index') }}"
                           class="mt-2 inline-block text-blue-600 hover:underline">
                            Lihat Keranjang
                        </a>

                    @endif

                </div>

            @elseif($c->status_custom === 'DITOLAK')

                <p class="mt-5 border-t pt-4 text-sm text-red-700">
                    Penawaran custom ini telah ditolak.
                </p>

            @endif

        </div>

    @empty

        <div class="rounded-xl border bg-white p-10 text-center">
            <p class="text-gray-600">
                Kamu belum memiliki permintaan custom.
            </p>
        </div>

    @endforelse

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $daftar->links() }}
    </div>

</div>
@endsection
