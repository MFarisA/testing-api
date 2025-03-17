<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'tb_berita';

    protected $fillable = [
        'judul',
        'path_media',
        'link',
        'filename',
        'deskripsi',
        'waktu',
        'id_uploader',
        'id_kategori',
        'publish',
        'open',
        'cover',
        'keyword',
        'editor',
        'library',
        'redaktur',
        'waktu_publish',
        'program_id',
        'type',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'waktu_publish' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'id_uploader');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
