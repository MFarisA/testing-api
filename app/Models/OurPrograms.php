<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurPrograms extends Model
{
    use HasFactory;

    protected $table = 'v2_our_programs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'thumbnail',
        'judul',
        'deskripsi',
        'link',
        'urutan',
    ];

    public function getThumbnailAttribute($value)
    {
        return $this->generateStorageUrl($value);
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $this->cleanStorageUrl($value);
    }

    private function generateStorageUrl($path)
    {
        if (!$path) {
            return null;
        }

        $baseUrl = 'https://storage.tvku.tv/ourprograms';

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function cleanStorageUrl($path)
    {
        $baseUrl = 'https://storage.tvku.tv/ourprograms/';

        if (strpos($path, $baseUrl) === 0) {
            return substr($path, strlen($baseUrl));
        }

        return $path;
    }
}
