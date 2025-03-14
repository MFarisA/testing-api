<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'tb_program';
    protected $primaryKey = 'id_program';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'judul',
        'video',
        'thumbnail',
        'deskripsi',
        'deskripsi_pendek',
        'id_acara',
        'tanggal',
    ];

    public function acara()
    {
        return $this->belongsTo(Acara::class, 'id_acara', 'id_acara');
    }
}
