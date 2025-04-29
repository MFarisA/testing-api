<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalAcara extends Model
{
    use HasFactory;

    protected $table = 'tb_hari_acara';
    protected $primaryKey = 'id';
    public $timestamps = false; 

    protected $fillable = [
        'id_hari',
        'jam_awal',
        'jam_akhir',
        'acara',
        'link',
        'uploader',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function hari()
    {
        return $this->belongsTo(Hari::class, 'id_hari');
    }
}
