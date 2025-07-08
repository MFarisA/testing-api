<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentTrailerTranslation extends Model
{
    use HasFactory;

    protected $table = 'recenttrailer_translation';
    public $timestamps = false;

    protected $fillable = [
        'recenttrailer_id',
        'translation_id',
        'judul',
        'youtube_id',
        'date',
    ];

    public function recentTrailer()
    {
        return $this->belongsTo(RecentTrailer::class, 'recenttrailer_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }

    public function setYoutubeIdAttribute($value)
    {
        $this->attributes['youtube_id'] = $value ?? $this->recentTrailer?->youtube_id;
    }


}
