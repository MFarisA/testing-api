<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IklanTranslation extends Model
{
    use HasFactory;

    protected $table = 'marketing_translations';
    public $timestamps = false;

    protected $fillable = [
        'marketing_id',
        'translation_id',
        'judul',
        'foto',
        'isi',
    ];

    public function iklan()
    {
        return $this->belongsTo(Iklan::class, 'marketing_id','id');
    }
    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
    public function getFotoAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }
}
