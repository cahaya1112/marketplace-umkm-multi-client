<?php

namespace App\Modules\Umkm\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkm';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo_path',
        'address',
        'phone_number',
        'is_verified',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}