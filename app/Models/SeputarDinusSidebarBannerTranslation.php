<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSidebarBannerTranslation extends Model
{
    use HasFactory;

    protected $table = 'spt_dinus_sidebar_banner_translation';
    public $timestamps = false;

    protected $fillable = [
        'spt_dinus_banner_id',
        'translation_id',
        'gambar',
    ];

    public function sptDinusSidebarBanner()
    {
        return $this->belongsTo(SeputarDinusSidebarBanner::class, 'spt_dinus_banner_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
}
