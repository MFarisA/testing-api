<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    use HasFactory;

    protected $table = 'v2_home_slider';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'sub_judul',
        'gambar',
        'urutan',
        'url',
    ];

    public function getGambarAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setGambarAttribute($value)
    {
        $this->attributes['gambar'] = $value;
    }
}
