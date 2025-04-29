<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurPrograms extends Model
{
    use HasFactory;

    protected $table = 'v2_our_programs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'thumbnail',
        'judul',
        'deskripsi',
        'link',
        'urutan',
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
