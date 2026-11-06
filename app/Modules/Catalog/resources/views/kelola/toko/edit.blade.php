@extends('catalog::kelola.layout')

@section('title', 'Profil Toko')

@section('content')
@php
    $input = 'w-full rounded-xl border border-[#D2A874]/60 bg-white px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3E5D3C]';
    $logo = $umkm->gambar_logo
        ? (\Illuminate\Support\Str::startsWith($umkm->gambar_logo, ['http://', 'https://']) ? $umkm->gambar_logo : asset('storage/' . $umkm->gambar_logo))
        : null;
@endphp

<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="text-2xl font-bold text-[#232934]">Profil Toko</h1>
    <div class="mb-6"></div>

    @include('catalog::kelola.partials.menu')

    <form method="POST" enctype="multipart/form-data" action="{{ route('kelola.toko.update') }}"
          class="space-y-5 rounded-2xl bg-white p-6 shadow-sm">
        @csrf @method('PUT')

        <div class="flex items-center gap-3 text-sm">
            <span>Status toko:</span>
            <span class="rounded-full px-3 py-1 font-medium {{ $umkm->status === 'AKTIF' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">{{ $umkm->status }}</span>
            <span class="text-xs text-slate-500">(diatur oleh Admin Platform)</span>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Nama toko</label>
            <input type="text" name="nama" value="{{ old('nama', $umkm->nama) }}" class="{{ $input }}">
            @error('nama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="{{ $input }}">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
            @error('deskripsi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Alamat</label>
            <input type="text" name="alamat" value="{{ old('alamat', $umkm->alamat) }}" class="{{ $input }}">
            @error('alamat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">ID wilayah asal (untuk ongkir)</label>
                <input type="text" name="id_wilayah_asal" value="{{ old('id_wilayah_asal', $umkm->id_wilayah_asal) }}" class="{{ $input }}">
                @error('id_wilayah_asal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Kontak</label>
                <input type="text" name="kontak" value="{{ old('kontak', $umkm->kontak) }}" class="{{ $input }}">
                @error('kontak') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Logo toko (maks. 2 MB)</label>
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $umkm->nama }}" class="mb-2 h-24 w-24 rounded-lg object-cover">
            @endif
            <input type="file" name="gambar_logo" accept="image/*" class="text-sm">
            @error('gambar_logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-xl bg-[#3E5D3C] px-6 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
    </form>
</div>
@endsection