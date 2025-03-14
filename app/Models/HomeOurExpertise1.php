<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurExpertise1 extends Model
{
    use HasFactory;

    protected $table = 'v2_home_ourexpertise1';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'thumbnail',
        'judul',
        'deskripsi',
    ];
}
