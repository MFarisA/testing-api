<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurProgramsTranslation extends Model
{
    use HasFactory;

    protected $table = 'home_ourprograms_translation';

    protected $fillable = [
        'ourprogram_id',
        'translation_id',
        'thumbnail',
        'judul',
        'deskripsi',
        'link',
    ];

    public function ourProgram()
    {
        return $this->belongsTo(OurPrograms::class, 'ourprogram_id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
