<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaTranslation extends Model
{
    use HasFactory;

    protected $table = 'berita_translation';

    protected $fillable = [
        'berita_id',
        'translation_id',
        'judul',
        'links',
        'deskripsi',
        'cover',
        'keyword',
    ];

    public function berita()
    {
        return $this->belongsTo(Berita::class, 'berita_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
