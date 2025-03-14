<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurPrograms extends Model
{
    use HasFactory;

    protected $table = 'v2_our_programs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'thumbnail',
        'judul',
        'deskripsi',
        'link',
        'urutan',
    ];
}
