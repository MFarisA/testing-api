<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iklan extends Model
{
    
    use HasFactory;

    protected $table = 'tb_marketing';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'judul',
        'foto',
        'isi',
        'video'
    ];
}
