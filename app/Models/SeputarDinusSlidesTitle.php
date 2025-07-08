<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSlidesTitle extends Model
{
    use HasFactory;

    protected $table = 'v2_sptdinus_slides_title';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'judul',
        'urutan',
    ];

    public function translations()
    {
        return $this->hasMany(SeputarDinusSlidesTitleTranslation::class, 'spt_dinus_title_id');
    }
}
