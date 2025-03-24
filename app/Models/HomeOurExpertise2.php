<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurExpertise2 extends Model
{
    use HasFactory;

    protected $table = 'v2_home_ourexpertise2';
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'thumbnail',
        'judul',
    ];

    public function getThumbnailAttribute($value)
    {
        return $this->generateThumbnailUrl($value);
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $this->cleanStorageUrl($value);
    }

    private function generateThumbnailUrl($path)
    {
        if (!$path) {
            return null;
        }

        $baseUrl = 'https://storage.tvku.tv/ourexpertise';

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function cleanStorageUrl($path)
    {
        $baseUrl = 'https://storage.tvku.tv/ourexpertise/';

        if (strpos($path, $baseUrl) === 0) {
            return substr($path, strlen($baseUrl));
        }

        return $path;
    }
}
