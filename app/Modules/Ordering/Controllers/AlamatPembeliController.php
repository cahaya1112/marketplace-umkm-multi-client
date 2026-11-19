<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ordering\Models\AlamatPembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AlamatPembeliController extends Controller
{
    private function pastikanPembeli(): void
    {
        abort_unless(auth()->user()?->isPembeli(), 403);
    }

    public function index()
    {
        $this->pastikanPembeli();

        $alamat = AlamatPembeli::where(
            'id_pengguna',
            auth()->id()
        )->orderByDesc('id_alamat')->get();

        return view('ordering::alamat.index', compact('alamat'));
    }

    public function create()
    {
        $this->pastikanPembeli();

        return view('ordering::alamat.form', [
            'alamat' => new AlamatPembeli(),
            'aksi' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $this->pastikanPembeli();

        $data = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string|max:255',
            'id_wilayah_tujuan' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
        ]);

        $data['id_pengguna'] = auth()->id();

        AlamatPembeli::create($data);

        return redirect()->route('alamat.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->pastikanPembeli();

        $alamat = AlamatPembeli::where('id_pengguna', auth()->id())
            ->findOrFail($id);

        return view('ordering::alamat.form', [
            'alamat' => $alamat,
            'aksi' => 'Edit',
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->pastikanPembeli();

        $alamat = AlamatPembeli::where('id_pengguna', auth()->id())
            ->findOrFail($id);

        $data = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string|max:255',
            'id_wilayah_tujuan' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
        ]);

        $alamat->update($data);

        return redirect()->route('alamat.index')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->pastikanPembeli();

        $alamat = AlamatPembeli::where('id_pengguna', auth()->id())
            ->findOrFail($id);

        // Jangan hapus alamat yang sudah dipakai pesanan.
        if ($alamat->getConnection()
            ->table('pesanan')
            ->where('id_alamat', $alamat->id_alamat)
            ->exists()) {
            return back()->with(
                'error',
                'Alamat sudah digunakan dalam pesanan dan tidak dapat dihapus.'
            );
        }

        $alamat->delete();

        return redirect()->route('alamat.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function cariWilayah(Request $request)
    {
        $this->pastikanPembeli();

        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ]);

        $apiKey = config('services.rajaongkir.api_key');
        $baseUrl = rtrim(
            config('services.rajaongkir.base_url'),
            '/'
        );

        if (!$apiKey) {
            return response()->json([
                'message' => 'API Key RajaOngkir belum dikonfigurasi.',
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->timeout(20)->get(
                $baseUrl . '/destination/domestic-destination',
                [
                    'search' => $request->search,
                    'limit' => 20,
                    'offset' => 0,
                ]
            );

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Gagal mengambil data wilayah dari RajaOngkir.',
                ], 502);
            }

            return response()->json([
                'data' => $response->json('data', []),
            ]);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Tidak dapat terhubung ke layanan RajaOngkir.',
            ], 502);
        }
    }
}
