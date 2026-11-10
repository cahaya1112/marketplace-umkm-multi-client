<?php

namespace App\Modules\Ordering\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $primaryKey = 'id_keranjang';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_pengguna',
            'id_pengguna'
        );
    }

    public function item(): HasMany
    {
        return $this->hasMany(
            KeranjangItem::class,
            'id_keranjang',
            'id_keranjang'
        );
    }
}