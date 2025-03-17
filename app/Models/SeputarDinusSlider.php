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
}
