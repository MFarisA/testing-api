<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSliderTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_slider_translation';
    public $timestamps = false;

    protected $fillable = [
        'slider_id',
        'translation_id',
        'judul',
        'sub_judul',
        'gambar',
        'urutan',
        'url',
    ];

    public function slider()
    {
        return $this->belongsTo(HomeSlider::class, 'slider_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }

    public function getGambarAttribute($value)
    {
        return $value ?? $this->slider?->gambar;
    }

    public function getUrutanAttribute($value)
    {
        return $value ?? $this->slider?->urutan;
    }

    public function getUrlAttribute($value)
    {
        return $value ?? $this->slider?->url;
    }
}
