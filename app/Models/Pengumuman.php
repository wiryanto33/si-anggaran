<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'file',
        'aktif',
        'publish_at',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'publish_at' => 'datetime',
    ];
}
