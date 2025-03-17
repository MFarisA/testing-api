<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
