<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class HomeWhoWeAre extends Model
{
    use HasFactory;

    protected $table = 'v2_home_whoweare';
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'motto1',
        'motto2',
        'motto3',
        'motto1sub',
        'motto2sub',
        'motto3sub',
    ];
}
