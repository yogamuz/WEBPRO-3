<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }

    // Tambahkan relasi ke AmbilAntrian
    public function ambilantrians()
    {
        return $this->hasMany(Ambilantrian::class, 'antrian_id', 'id');
    }
}