<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTranslation extends Model
{
    use HasFactory;

    protected $table = 'kategori_translations';
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'slug',
        'id_kategori',
        'translation_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
}
