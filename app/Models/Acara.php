<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Acara extends Model
{
    use HasFactory;

    protected $table = 'tb_acara';
    protected $primaryKey = 'id_acara';

    protected $fillable = [
        'nama_acara',
        'thumbnail_acara',
        'description',
        'path',
    ];

    /**
     * Getter for thumbnail_acara attribute.
     */
    public function getThumbnailAcaraAttribute($value)
    {
        return $value ? asset(config('app.tvku_storage.thumbnail_berita_path') . '/' . $value) : null;
    }

    /**
     * Setter for thumbnail_acara attribute.
     */
    public function setThumbnailAcaraAttribute($value)
    {
        $this->attributes['thumbnail_acara'] = $value ? str_replace(
            asset(config('app.tvku_storage.thumbnail_berita_path') . '/'),
            '',
            $value
        ) : null;
    }
}