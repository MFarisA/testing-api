<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSlider extends Model
{
    use HasFactory;

    protected $table = 'v2_sptdinus_slider';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_slides_title',
        'thumbnail',
        'thumbnail_hover',
        'teks',
        'link',
        'deskripsi',
    ];

    public function getThumbnailAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
    }

    public function getThumbnailHoverAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setThumbnailHoverAttribute($value)
    {
        $this->attributes['thumbnail_hover'] = $value;
    }
}
