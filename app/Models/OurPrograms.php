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
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
    }

    public function translations()
    {
        return $this->hasMany(OurProgramsTranslation::class, 'ourprogram_id', 'id');
    } 
}
