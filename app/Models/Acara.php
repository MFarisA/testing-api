<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acara extends Model
{
    use HasFactory;

    protected $table = 'tb_acara';
    public $timestamps = false;
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
        $baseUrl = config('app.tvku_storage.base_url', env('APP_URL') . '/storage');
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $value ? $baseUrl . '/' . $value : null;
    }

    /**
     * Setter for thumbnail_acara attribute.
     */
    public function setThumbnailAcaraAttribute($value)
    {
        $this->attributes['thumbnail_acara'] = $value;
    }

    public function translations()
    {
        return $this->hasMany(AcaraTranslation::class, 'acara_id', 'id_acara');
    }
}