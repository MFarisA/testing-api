<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSliderTranslation extends Model
{
    use HasFactory;

    protected $table = 'spt_dinus_slider_translation';
    public $timestamps = false;

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
        return $this->belongsTo(SeputarDinusSlider::class, 'spt_dinus_slider_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }

    public function getThumbnailAttribute()
    {
        return $this->sptDinusSlider->thumbnail ?? null;
    }

    public function getThumbnailHoverAttribute()
    {
        return $this->sptDinusSlider->thumbnail_hover ?? null;
    }

    public function getLinkAttribute($value)
    {
        if (!$value) {
            return $this->sptDinusSlider?->link;
        }
        return $value;
    }
}
