<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeWhoWeAreTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_whoweare_translation';

    protected $fillable = [
        'whoweare_id',
        'translation_id',
        'judul',
        'deskripsi',
        'gambar',
        'motto1',
        'motto2',
        'motto3',
        'motto1sub',
        'motto2sub',
        'motto3sub',
    ];

    public function whoWeAre()
    {
        return $this->belongsTo(HomeWhoWeAre::class, 'whoweare_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
