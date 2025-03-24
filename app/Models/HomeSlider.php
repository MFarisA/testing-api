<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    use HasFactory;

    protected $table = 'v2_home_slider';
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'sub_judul',
        'gambar',
        'urutan',
        'url',
    ];

    public function getGambarAttribute($value)
    {
        return $this->generateImageUrl($value);
    }

    public function setGambarAttribute($value)
    {
        $this->attributes['gambar'] = $this->cleanStorageUrl($value);
    }

    private function generateImageUrl($path)
    {
        if (!$path) {
            return null;
        }

        $baseUrl = 'https://storage.tvku.tv/slider';

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function cleanStorageUrl($path)
    {
        $baseUrl = 'https://storage.tvku.tv/slider/';

        if (strpos($path, $baseUrl) === 0) {
            return substr($path, strlen($baseUrl));
        }

        return $path;
    }
}
