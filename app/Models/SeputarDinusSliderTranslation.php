<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSliderTranslation extends Model
{
    use HasFactory;

    protected $table = 'spt_dinus_slider_translation';

    protected $fillable = [
        'spt_dinus_slider_id',
        'translation_id',
        'thumbnail',
        'thumbnail_hover',
        'teks',
        'link',
        'deskripsi',
    ];

    public function sptDinusSlider()
    {
        return $this->belongsTo(SeputarDinusSlider::class, 'spt_dinus_slider_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
