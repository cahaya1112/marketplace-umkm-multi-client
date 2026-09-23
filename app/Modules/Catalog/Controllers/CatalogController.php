<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;

class CatalogController extends Controller
{
    public function index()
    {
        return response()->json([
            'module' => 'Catalog',
            'message' => 'Katalog produk UMKM',
        ]);
    }

    public function manage()
    {
        return response()->json([
            'module' => 'Catalog',
            'message' => 'Manajemen katalog UMKM',
        ]);
    }
}
