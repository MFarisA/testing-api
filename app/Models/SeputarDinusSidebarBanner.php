<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSidebarBanner extends Model
{
    use HasFactory;

    protected $table = 'v2_sptudinus_sidebar_banner';

    protected $fillable = [
        'gambar',
    ];

    protected $casts = [
        'gambar' => 'string',
    ];

    public function getGambarAttribute($value)
    {
        return $this->generateStorageUrl($value);
    }

    public function setGambarAttribute($value)
    {
        $this->attributes['gambar'] = $this->cleanStorageUrl($value);
    }

    private function generateStorageUrl($path)
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $baseUrl = 'https://storage.tvku.tv/sptdnsidebarbanner';

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function cleanStorageUrl($path)
    {
        $baseUrl = 'https://storage.tvku.tv/sptdnsidebarbanner/';

        if (strpos($path, $baseUrl) === 0) {
            return substr($path, strlen($baseUrl));
        }

        return $path;
    }
}
