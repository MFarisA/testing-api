<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationToken extends Model
{
    protected $fillable = ['token'];
    public function categories()
    {
        return $this->belongsToMany(NotificationCategory::class,'notification_category_token');
    }
}
