<?php

namespace App\Modules\CustomRequest\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CustomRequest\Models\PermintaanCustom;
use Illuminate\Http\Request;

class CustomRequestController extends Controller
{
    public function index()
    {
        return response()->json([
            'module' => 'CustomRequest',
            'data' => PermintaanCustom::latest('tanggal_permintaan')->get(),
        ]);
    }

    public function manage()
    {
        return response()->json([
            'module' => 'CustomRequest',
            'data' => PermintaanCustom::latest('tanggal_permintaan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_item_pesanan' => ['required', 'integer'],
            'spesifikasi_custom' => ['required', 'string'],
        ]);

        $data['status_custom'] = 'MENUNGGU';
        $data['tanggal_permintaan'] = now();

        $custom = PermintaanCustom::create($data);

        return response()->json([
            'message' => 'Permintaan custom berhasil diajukan.',
            'data' => $custom,
        ], 201);
    }

    public function respond(Request $request, $id)
    {
        $data = $request->validate([
            'status_custom' => ['required', 'in:DITERIMA,DITOLAK'],
            'harga_custom' => ['nullable', 'numeric'],
            'alasan_penolakan' => ['nullable', 'string'],
        ]);

        $custom = PermintaanCustom::findOrFail($id);

        $custom->update([
            'status_custom' => $data['status_custom'],
            'harga_custom' => $data['harga_custom'] ?? null,
            'alasan_penolakan' => $data['alasan_penolakan'] ?? null,
            'tanggal_respon' => now(),
            'id_admin_umkm_respon' => auth()->user()->id ?? null,
        ]);

        return response()->json([
            'message' => 'Permintaan custom berhasil diproses.',
            'data' => $custom,
        ]);
    }
}
