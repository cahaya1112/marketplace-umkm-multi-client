@extends('catalog::kelola.layout')

@section('title', $produk->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
@php
    $edit = $produk->exists;
    $input = 'w-full rounded-xl border border-[#D2A874]/60 bg-white px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3E5D3C]';
@endphp

<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="text-2xl font-bold text-[#232934]">{{ $edit ? 'Edit Produk' : 'Tambah Produk' }}</h1>
    <div class="mb-6"></div>

    @include('catalog::kelola.partials.menu')

    <form method="POST" enctype="multipart/form-data"
          action="{{ $edit ? route('kelola.produk.update', $produk->id_produk) : route('kelola.produk.store') }}"
          class="space-y-5 rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div>
            <label class="mb-1 block text-sm font-medium">Nama produk</label>
            <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}" class="{{ $input }}">
            @error('nama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Kategori</label>
            <select name="id_kategori" class="{{ $input }}">
                <option value="">Pilih kategori</option>
                @foreach ($daftarKategori as $kat)
                    <option value="{{ $kat->id_kategori }}" @selected(old('id_kategori', $produk->id_kategori) == $kat->id_kategori)>{{ $kat->nama }}</option>
                @endforeach
            </select>
            @error('id_kategori') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="{{ $input }}">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            @error('deskripsi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Harga (Rp)</label>
                <input type="number" min="0" name="harga_retail" value="{{ old('harga_retail', $produk->harga_retail) }}" class="{{ $input }}">
                @error('harga_retail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Berat (gram)</label>
                <input type="number" min="1" name="berat_gram" value="{{ old('berat_gram', $produk->berat_gram) }}" class="{{ $input }}">
                @error('berat_gram') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Jumlah stok</label>
                <input type="number" min="0" name="jumlah_stok" value="{{ old('jumlah_stok', $produk->stok->jumlah_stok ?? 0) }}" class="{{ $input }}">
                @error('jumlah_stok') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Batas minimum stok</label>
                <input type="number" min="0" name="batas_minimum" value="{{ old('batas_minimum', $produk->stok->batas_minimum ?? 5) }}" class="{{ $input }}">
                @error('batas_minimum') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Foto produk (maks. 2 MB)</label>
            @if ($edit && $produk->gambar_url)
                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}" class="mb-2 h-24 w-24 rounded-lg object-cover">
            @endif
            <input type="file" name="gambar" accept="image/*" class="text-sm">
            @error('gambar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-[#3E5D3C] px-6 py-2 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('kelola.produk.index') }}" class="rounded-xl border border-[#E3D9BF] px-6 py-2 text-sm font-medium">Batal</a>
        </div>
    </form>
</div>
@endsection