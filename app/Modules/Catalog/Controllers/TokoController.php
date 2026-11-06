<?php

namespace App\Modules\Catalog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TokoController extends KelolaController
{
    public function edit()
    {
        return view('catalog::kelola.toko.edit', ['umkm' => $this->umkmSaya()]);
    }

    public function update(Request $request)
    {
        $umkm = $this->umkmSaya();

        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:150'],
            'deskripsi'       => ['nullable', 'string', 'max:2000'],
            'alamat'          => ['required', 'string', 'max:255'],
            'id_wilayah_asal' => ['required', 'string', 'max:50'],
            'kontak'          => ['required', 'string', 'max:50'],
            'gambar_logo'     => ['nullable', 'image', 'max:2048'],
        ]);

        // Hanya kolom ini yang boleh diubah. Status (AKTIF/NONAKTIF) tetap hak Admin Platform.
        $atribut = Arr::only($data, ['nama', 'deskripsi', 'alamat', 'id_wilayah_asal', 'kontak']);
        $atribut['deskripsi'] = $atribut['deskripsi'] ?? '';

        if ($request->hasFile('gambar_logo')) {
            $this->hapusFile($umkm->gambar_logo);
            $atribut['gambar_logo'] = $this->simpanFile($request, 'gambar_logo', 'umkm');
        }

        $umkm->update($atribut);

        return redirect()->route('kelola.toko.edit')->with('sukses', 'Profil toko berhasil diperbarui.');
    }
}