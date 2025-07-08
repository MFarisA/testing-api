<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcaraTranslation extends Model
{
    use HasFactory;

    protected $table = 'acara_translation';
    public $timestamps = false;

    protected $fillable = [
        'acara_id',
        'translation_id',
        'nama_acara',
        'thumbnail_acara',
        'description',
    ];

    public function acara()
    {
        return $this->belongsTo(Acara::class, 'acara_id', 'id_acara');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }

    public function getThumbnailAcaraAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }
}
