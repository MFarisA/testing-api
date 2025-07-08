<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{
    protected $fillable = ['name'];
    public function tokens()
    {
        return $this->belongsToMany(NotificationToken::class,'notification_category_token');
    }
}
