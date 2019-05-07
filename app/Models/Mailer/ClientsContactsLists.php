<?php

namespace App\Models\Mailer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsContactsLists extends Model
{
    protected $table = 'mailer_contacts_lists_contacts';
    protected $fillable = [
        'list_id', 'contact_id'
    ];
}
