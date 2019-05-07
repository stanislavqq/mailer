<?php

namespace App\Models\Mailer;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MailerDriver extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }


}
