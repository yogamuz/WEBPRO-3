<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message'
    ];

    // Mengubah nama menjadi kapital saat menyimpan
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    // Accessor untuk format tanggal
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d F Y H:i');
    }
       protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_read' => 'boolean'
    ];

    /**
     * The attributes that should be mutated to dates.
     * (Untuk Laravel versi lama)
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Scope untuk filter pesan yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope untuk filter pesan yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Accessor untuk mendapatkan format tanggal yang mudah dibaca
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i') : '-';
    }

    /**
     * Accessor untuk mendapatkan preview pesan
     */
    public function getMessagePreviewAttribute()
    {
        return strlen($this->message) > 100 ? substr($this->message, 0, 100) . '...' : $this->message;
    }
}