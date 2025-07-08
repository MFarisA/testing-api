<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeWhoWeAreTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_whoweare_translation';
    public $timestamps = false;

    protected $fillable = [
        'whoweare_id',
        'translation_id',
        'judul',
        'deskripsi',
        'gambar',
        'motto1',
        'motto2',
        'motto3',
        'motto1sub',
        'motto2sub',
        'motto3sub',
    ];

    public function whoWeAre()
    {
        return $this->belongsTo(HomeWhoWeAre::class, 'whoweare_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
    
    public function getGambarAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
    
        if (!$value && $this->whoweare) {
            return $this->whoweare->gambar;
        }
    
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setGambarAttribute($value)
    {
        return $value ?? $this->whoWeAre?->gambar;
    }
}
