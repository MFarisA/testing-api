<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSlider extends Model
{
    use HasFactory;

    protected $table = 'v2_sptdinus_slider';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_slides_title',
        'thumbnail',
        'thumbnail_hover',
        'teks',
        'link',
        'deskripsi',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->thumbnail = $model->cleanStorageUrl($model->thumbnail);
            $model->thumbnail_hover = $model->cleanStorageUrl($model->thumbnail_hover);
        });
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->generateStorageUrl($this->thumbnail);
    }

    public function getThumbnailHoverUrlAttribute()
    {
        return $this->generateStorageUrl($this->thumbnail_hover);
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $this->cleanStorageUrl($value);
    }

    public function setThumbnailHoverAttribute($value)
    {
        $this->attributes['thumbnail_hover'] = $this->cleanStorageUrl($value);
    }

    private function generateStorageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $baseUrl = 'https://storage.tvku.tv/sptdnslider';
        return "{$baseUrl}/" . ltrim($path, '/');
    }

    private function cleanStorageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $baseUrl = 'https://storage.tvku.tv/sptdnslider/';

        return str_starts_with($path, $baseUrl) ? substr($path, strlen($baseUrl)) : $path;
    }
}
