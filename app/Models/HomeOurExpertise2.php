<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurExpertise2 extends Model
{
    use HasFactory;

    protected $table = 'v2_home_ourexpertise2';
    public $timestamps = false;
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'thumbnail',
        'judul',
    ];

    public function getThumbnailAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
    }
}
