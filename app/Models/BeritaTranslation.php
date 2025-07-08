<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaTranslation extends Model
{
    use HasFactory;

    protected $table = 'berita_translation';
    public $timestamps = false;

    protected $fillable = [
        'berita_id',
        'translation_id',
        'judul',
        'path_media',
        'link',
        'deskripsi',
        'cover',
        'keyword',
    ];

    public function berita()
    {
        return $this->belongsTo(Berita::class, 'berita_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }

    public function getCoverAttribute($value)
{
    $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
    $thumbnailPath = config('app.tvku_storage.thumbnail_berita_path', 'thumbnails/berita');
    $pathMedia = $this->path_media ?? '';

    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return $value;
    }

    return $value ? $baseUrl . '/' . trim($thumbnailPath, '/') . '/' . trim($pathMedia, '/') . '/' . ltrim($value, '/') : null;
}
}
