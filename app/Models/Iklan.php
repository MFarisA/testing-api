<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iklan extends Model
{
    
    use HasFactory;

    protected $table = 'tb_marketing';
    public $timestamps = false;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'judul',
        'foto',
        'isi',
        'video',
        'user_id'
    ];

    public function getFotoAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }

    public function setFotoAttribute($value)
    {
        $this->attributes['foto'] = $value;
    }

    public function translations()
    {
        return $this->hasMany(IklanTranslation::class, 'marketing_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
