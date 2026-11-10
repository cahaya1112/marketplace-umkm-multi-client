<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase as BaseTestCase;

class KeranjangValidationTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tabel keranjang untuk database pengujian.
        Schema::create('keranjang', function (Blueprint $table) {
            $table->increments('id_keranjang');
            $table->unsignedInteger('id_pengguna')->unique();
        });

        Schema::create('keranjang_item', function (Blueprint $table) {
            $table->increments('id_keranjang_item');
            $table->unsignedInteger('id_keranjang');
            $table->unsignedInteger('id_produk');
            $table->integer('jumlah');
        });

        Schema::create('stok', function (Blueprint $table) {
            $table->increments('id_stok');
            $table->unsignedInteger('id_produk')->unique();
            $table->integer('jumlah_stok');
            $table->integer('batas_minimum')->default(0);
        });

        // Data awal untuk pengujian.
        DB::table('keranjang')->insert([
            'id_keranjang' => 1,
            'id_pengguna' => 4,
        ]);

        DB::table('keranjang_item')->insert([
            'id_keranjang_item' => 1,
            'id_keranjang' => 1,
            'id_produk' => 6,
            'jumlah' => 5,
        ]);

        DB::table('stok')->insert([
            'id_stok' => 1,
            'id_produk' => 6,
            'jumlah_stok' => 25,
            'batas_minimum' => 5,
        ]);

        // Simulasi pengguna yang sudah login.
        $user = new User();
        $user->forceFill([
            'id_pengguna' => 4,
            'peran' => 'PEMBELI',
        ]);

        $this->actingAs($user);
    }

    public function test_jumlah_melebihi_stok_ditolak(): void
    {
        $response = $this->patchJson('/keranjang/1', [
            'jumlah' => 26,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('jumlah');

        $this->assertDatabaseHas('keranjang_item', [
            'id_keranjang_item' => 1,
            'jumlah' => 5,
        ]);
    }

    public function test_jumlah_sesuai_stok_berhasil_diperbarui(): void
    {
        $response = $this->patchJson('/keranjang/1', [
            'jumlah' => 10,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('keranjang_item', [
            'id_keranjang_item' => 1,
            'jumlah' => 10,
        ]);
    }
    
    public function test_pengguna_lain_tidak_bisa_mengubah_atau_menghapus_keranjang(): void
    {
        // Simulasi Customer B yang bukan pemilik keranjang.
        $penggunaLain = new User();

        $penggunaLain->forceFill([
            'id_pengguna' => 5,
            'peran' => 'PEMBELI',
        ]);

        $this->actingAs($penggunaLain);

        // Customer B mencoba mengubah item milik Customer A.
        $responseUpdate = $this->patchJson('/keranjang/1', [
            'jumlah' => 10,
        ]);

        $responseUpdate->assertNotFound();

        // Pastikan jumlah produk tidak berubah.
        $this->assertDatabaseHas('keranjang_item', [
            'id_keranjang_item' => 1,
            'jumlah' => 5,
        ]);

        // Customer B mencoba menghapus item milik Customer A.
        $responseDelete = $this->deleteJson('/keranjang/1');

        $responseDelete->assertNotFound();

        // Pastikan item masih ada.
        $this->assertDatabaseHas('keranjang_item', [
            'id_keranjang_item' => 1,
            'jumlah' => 5,
        ]);
    }
}
