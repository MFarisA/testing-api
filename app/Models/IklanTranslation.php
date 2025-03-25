<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IklanTranslation extends Model
{
    use HasFactory;

    protected $table = 'marketing_translation';

    protected $fillable = [
        'marketing_id',
        'locale',
        'judul',
        'isi',
    ];

    public function marketing()
    {
        return $this->belongsTo(Iklan::class, 'marketing_id');
    }
}
