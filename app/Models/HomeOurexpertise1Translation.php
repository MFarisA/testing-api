<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurexpertise1Translation extends Model
{
    use HasFactory;

    protected $table = 'home_ourexpertise1_translation';
    public $timestamps = false;

    protected $fillable = [
        'ourexpertise1_id',
        'translation_id',
        'thumbnail',
        'judul',
        'deskripsi',
    ];
    protected $appends = ['thumbnail'];


    public function ourexpertise1()
    {
        return $this->belongsTo(HomeOurExpertise1::class, 'ourexpertise1_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
    
    public function getThumbnailAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }
}
