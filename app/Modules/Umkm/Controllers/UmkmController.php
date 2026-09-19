<?php

namespace App\Modules\Umkm\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Daftar UMKM']);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Detail UMKM ' . $id]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Tambah UMKM']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Update UMKM ' . $id]);
    }
}