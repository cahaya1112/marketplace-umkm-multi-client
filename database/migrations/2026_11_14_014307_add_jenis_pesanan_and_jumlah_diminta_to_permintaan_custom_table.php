
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_custom', function (Blueprint $table) {
            $table->enum('jenis_pesanan', [
                'CUSTOM_LANGSUNG',
                'CUSTOM_DARI_STOK',
            ])->default('CUSTOM_LANGSUNG')
              ->after('id_keranjang_item');

            $table->unsignedInteger('jumlah_diminta')
                ->default(1)
                ->after('jenis_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_custom', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_pesanan',
                'jumlah_diminta',
            ]);
        });
    }
};
