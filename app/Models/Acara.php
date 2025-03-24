<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acara extends Model
{
    use HasFactory;

    protected $table = 'tb_acara'; 
    protected $primaryKey = 'id_acara'; 

    protected $fillable = [
        'nama_acara',
        'thumbnail_acara',
        'description',
        'path',
    ];

    public function getThumbnailAcaraAttribute($value)
    {
        return $this->generateThumbnailUrl($value);
    }

    public function setThumbnailAcaraAttribute($value)
    {
        $this->attributes['thumbnail_acara'] = $this->cleanStorageUrl($value);
    }

    private function generateThumbnailUrl($path)
    {
        if (!$path) {
            return null;
        }

        $baseUrl = 'https://storage.tvku.tv/acara';

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function cleanStorageUrl($path)
    {
        $baseUrl = 'https://storage.tvku.tv/acara/';

        if (strpos($path, $baseUrl) === 0) {
            return substr($path, strlen($baseUrl));
        }

        return $path;
    }
}
