<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSidebarBanner extends Model
{
    use HasFactory;

    protected $table = 'v2_sptudinus_sidebar_banner';
    public $timestamps = false;

    protected $fillable = [
        'gambar',
    ];

    protected $casts = [
        'gambar' => 'string',
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
