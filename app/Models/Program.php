<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'tb_program_acara';
    protected $primaryKey = 'id_program';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'video',
        'thumbnail',
        'deskripsi',
        'deskripsi_pendek',
        'id_acara',
        'tanggal',
    ];

    public function acara()
    {
        return $this->belongsTo(Acara::class, 'id_acara', 'id_acara');
    }

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
