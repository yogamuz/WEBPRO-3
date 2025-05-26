<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambilantrian extends Model
{
    use HasFactory;

    // PERBAIKAN: Pastikan fillable tidak mengandung field yang tidak ada di tabel
    protected $fillable = [
        'tanggal', 
        'nama_lengkap', 
        'alamat', 
        'kode', 
        'nomorhp', 
        'antrian_id', 
        'user_id'
        // Hapus 'batas_antrian' karena ini field dari tabel antrians, bukan ambilantrians
        // Hapus 'created_at' karena Laravel otomatis handle timestamps
    ];

    // Tambahkan casting untuk memastikan tipe data benar
    protected $casts = [
        'tanggal' => 'date',
        'antrian_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Timestamps otomatis dihandle Laravel
    public $timestamps = true;

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}