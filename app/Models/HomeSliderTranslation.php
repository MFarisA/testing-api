<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSliderTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_slider_translation';

    protected $fillable = [
        'slider_id',
        'translation_id',
        'gambar',
    ];

    public function slider()
    {
        return $this->belongsTo(HomeSlider::class, 'slider_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
