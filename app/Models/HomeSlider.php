<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    use HasFactory;

    protected $table = 'v2_home_slider';
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'sub_judul',
        'gambar',
        'urutan',
        'url',
    ];
}
