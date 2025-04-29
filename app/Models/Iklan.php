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
        'video'
    ];

    /**
     * Getter for foto attribute.
     */
    public function getFotoAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        return $value ? $baseUrl . '/' . $value : null;
    }

    /**
     * Setter for foto attribute.
     */
    public function setFotoAttribute($value)
    {
        $this->attributes['foto'] = $value;
    }
}
