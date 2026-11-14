<?php

namespace App\Modules\Ordering\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlamatPembeli extends Model
{
    protected $table = 'alamat_pembeli';

    protected $primaryKey = 'id_alamat';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'nama_penerima',
        'nomor_telepon',
        'alamat_lengkap',
        'id_wilayah_tujuan',
        'kode_pos',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_pengguna',
            'id_pengguna'
        );
    }
}