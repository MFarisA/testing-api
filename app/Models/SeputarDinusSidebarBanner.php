<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeputarDinusSidebarBanner extends Model
{
    use HasFactory;

    protected $table = 'v2_sptudinus_sidebar_banner';

    protected $fillable = [
        'gambar',
    ];
}
