<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'program';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'program',
        'path',
        'link', 
    ];

}
