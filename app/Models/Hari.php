<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    protected $table = 'tb_hari';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'hari',
    ];

    public function jadwalAcara()
    {
        return $this->hasMany(JadwalAcara::class, 'id_hari');
    }
}
