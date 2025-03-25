<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSlidesTitleTranslation extends Model
{
    use HasFactory;

    protected $table = 'spt_dinus_slides_title_translation';

    protected $fillable = [
        'spt_dinus_slides_title_id',
        'translation_id',
        'judul',
    ];

    public function sptDinusSlidesTitle()
    {
        return $this->belongsTo(SeputarDinusSlidesTitle::class, 'spt_dinus_slides_title_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
