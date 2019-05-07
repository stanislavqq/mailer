<?php

namespace App\Models\Mailer;

use Illuminate\Database\Eloquent\Model;

class UserMailerDrivers extends Model
{
    protected $table = 'user_mailer_drivers';
    protected $fillable = ['login', 'password', 'user_id', 'driver_id'];
    protected $primaryKey = 'user_id';
}
