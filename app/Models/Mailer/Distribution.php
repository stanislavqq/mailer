<?php

namespace App\Models\Mailer;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $table = 'mailer_distributions';
    protected $fillable = [
        'name', 'description', 'active', 'subject', 'from_email', 'from_name', 'contacts_list_id', 'template_id', 'send_date', 'last_send_date', 'user_id'
    ];

    public function contactList()
    {
        return $this->hasMany(ContactList::class, 'id', 'contacts_list_id');
    }

    public function template()
    {
        return $this->hasMany(Template::class, 'id', 'template_id');
    }
}
