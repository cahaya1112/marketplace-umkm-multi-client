
@extends('layouts.pelanggan')

@section('title', $aksi . ' Alamat')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">

    <h1 class="text-2xl font-bold mb-6">
        {{ $aksi }} Alamat Pengiriman
    </h1>

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-5">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ $aksi === 'Tambah'
              ? route('alamat.store')
              : route('alamat.update', $alamat->id_alamat) }}"
          class="bg-white border rounded-2xl p-6 space-y-5">

        @csrf

        @if($aksi === 'Edit')
            @method('PUT')
        @endif

        <div>
            <label class="block font-medium mb-2">Nama Penerima</label>
            <input type="text" name="nama_penerima"
                   value="{{ old('nama_penerima', $alamat->nama_penerima) }}"
                   required maxlength="255"
                   class="w-full border rounded-xl p-3">
        </div>

        <div>
            <label class="block font-medium mb-2">Nomor Telepon</label>
            <input type="text" name="nomor_telepon"
                   value="{{ old('nomor_telepon', $alamat->nomor_telepon) }}"
                   required maxlength="255"
                   class="w-full border rounded-xl p-3">
        </div>

        <div>
            <label class="block font-medium mb-2">
                Cari Wilayah Tujuan
            </label>

            <div class="flex gap-2">
                <input id="searchWilayah" type="text"
                       placeholder="Contoh: Rogojampi, Banyuwangi"
                       class="w-full border rounded-xl p-3">

                <button type="button" id="btnCari"
                        class="bg-green-800 text-white px-5 rounded-xl">
                    Cari
                </button>
            </div>

            <div id="hasilWilayah"
                 class="mt-2 space-y-2"></div>

            <p id="wilayahTerpilih"
               class="text-sm text-green-800 mt-2"></p>
        </div>

        <div>
            <label class="block font-medium mb-2">Alamat Lengkap</label>
            <textarea name="alamat_lengkap" required maxlength="255"
                      rows="3"
                      class="w-full border rounded-xl p-3">{{ old('alamat_lengkap', $alamat->alamat_lengkap) }}</textarea>
        </div>

        <input type="hidden" name="id_wilayah_tujuan"
               id="id_wilayah_tujuan"
               value="{{ old('id_wilayah_tujuan', $alamat->id_wilayah_tujuan) }}">

        <div>
            <label class="block font-medium mb-2">Kode Pos</label>
            <input type="text" name="kode_pos" id="kode_pos"
                   value="{{ old('kode_pos', $alamat->kode_pos) }}"
                   required maxlength="255"
                   class="w-full border rounded-xl p-3">
        </div>

        <div class="flex gap-3">
            <a href="{{ route('alamat.index') }}"
               class="border px-5 py-3 rounded-xl">
                Batal
            </a>

            <button type="submit"
                    class="bg-green-800 text-white px-6 py-3 rounded-xl">
                Simpan Alamat
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('btnCari').addEventListener('click', async () => {
    const keyword = document.getElementById('searchWilayah').value.trim();
    const hasil = document.getElementById('hasilWilayah');

    if (keyword.length < 2) {
        alert('Masukkan minimal 2 karakter.');
        return;
    }

    hasil.innerHTML = '<p class="text-slate-500">Mencari wilayah...</p>';

    try {
        const response = await fetch(
            '{{ route("alamat.wilayah") }}?search=' + encodeURIComponent(keyword),
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );

        const json = await response.json();

        if (!response.ok) {
            throw new Error(json.message || 'Gagal mencari wilayah.');
        }

        hasil.innerHTML = '';

        if (!json.data || json.data.length === 0) {
            hasil.innerHTML = '<p>Wilayah tidak ditemukan.</p>';
            return;
        }

        json.data.forEach(wilayah => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className =
                'block w-full text-left border rounded-xl p-3 hover:bg-green-50';

            button.textContent = wilayah.label;

            button.addEventListener('click', () => {
                document.getElementById('id_wilayah_tujuan').value =
                    wilayah.id;

                document.getElementById('kode_pos').value =
                    wilayah.zip_code || '';

                document.getElementById('wilayahTerpilih').textContent =
                    'Wilayah dipilih: ' + wilayah.label +
                    ' (ID: ' + wilayah.id + ')';

                hasil.innerHTML = '';
            });

            hasil.appendChild(button);
        });

    } catch (error) {
        hasil.innerHTML = '';
        alert(error.message);
    }
});
</script>
@endsection
