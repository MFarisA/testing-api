<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurexpertise1Translation extends Model
{
    use HasFactory;

    protected $table = 'home_ourexpertise1_translation';

    protected $fillable = [
        'ourexpertise1_id',
        'translation_id',
        'thumbnail',
        'judul',
        'deskripsi',
    ];

    public function ourexpertise1()
    {
        return $this->belongsTo(HomeOurExpertise1::class, 'ourexpertise1_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
