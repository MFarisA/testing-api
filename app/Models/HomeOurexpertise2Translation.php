<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurexpertise2Translation extends Model
{
    use HasFactory;

    protected $table = 'home_ourexpertise2_translation';

    protected $fillable = [
        'ourexpertise2_id',
        'translation_id',
        'thumbnail',
        'judul',
    ];

    public function ourexpertise2()
    {
        return $this->belongsTo(HomeOurExpertise2::class, 'ourexpertise2_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
