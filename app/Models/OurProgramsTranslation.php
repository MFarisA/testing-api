<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurProgramsTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_ourprograms_translation';
    public $timestamps = false;

    protected $fillable = [
        'ourprogram_id',
        'translation_id',
        'thumbnail',
        'judul',
        'deskripsi',
        'link',
        'urutan',
    ];

    public function ourProgram()
    {
        return $this->belongsTo(OurPrograms::class, 'ourprogram_id', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
    }

    public function getThumbnailAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function getUrutanAttribute($value)
    {
        return $value ?? $this->ourProgram?->urutan;
    }
}
