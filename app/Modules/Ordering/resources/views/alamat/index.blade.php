
@extends('layouts.pelanggan')

@section('title', 'Alamat Saya')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Alamat Saya
            </h1>
            <p class="text-slate-500 mt-1">
                Kelola alamat pengiriman pesananmu.
            </p>
        </div>

        <a href="{{ route('alamat.create') }}"
           class="bg-green-800 text-white px-5 py-3 rounded-xl">
            + Tambah Alamat
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-800 p-4 rounded-xl mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-4">
            {{ session('error') }}
        </div>
    @endif

    @forelse($alamat as $item)
        <div class="bg-white border rounded-xl p-5 mb-4">
            <div class="flex justify-between gap-4">
                <div>
                    <h3 class="font-bold text-lg">
                        {{ $item->nama_penerima }}
                    </h3>

                    <p class="text-slate-600">
                        {{ $item->nomor_telepon }}
                    </p>

                    <p class="mt-2">
                        {{ $item->alamat_lengkap }}
                    </p>

                    <p class="text-sm text-slate-500 mt-1">
                        ID Wilayah: {{ $item->id_wilayah_tujuan }}
                        · Kode Pos: {{ $item->kode_pos }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('alamat.edit', $item->id_alamat) }}"
                       class="text-blue-700 font-medium">
                        Edit
                    </a>

                    <form action="{{ route('alamat.destroy', $item->id_alamat) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus alamat ini?')">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-600 font-medium">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border rounded-xl p-8 text-center">
            <p class="text-slate-500">
                Kamu belum memiliki alamat pengiriman.
            </p>
        </div>
    @endforelse

    <a href="{{ route('keranjang.index') }}"
       class="inline-block mt-5 text-green-800">
        ← Kembali ke keranjang
    </a>
</div>
@endsection
