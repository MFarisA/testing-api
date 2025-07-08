<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentTrailer extends Model
{
    use HasFactory;

    protected $table = 'v2_recenttrailer';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'date',
        'youtube_id',
    ];

    public function translations()
    {
        return $this->hasMany(RecentTrailerTranslation::class, 'recenttrailer_id');
    }
}
