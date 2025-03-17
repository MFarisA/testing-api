<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOurExpertise2 extends Model
{
    use HasFactory;

    protected $table = 'v2_home_ourexpertise2';
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'thumbnail',
        'judul',
    ];

}
