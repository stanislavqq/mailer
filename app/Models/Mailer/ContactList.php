<?php

namespace App\Models\Mailer;

use App\Models\ClientsContact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactList extends Model
{
    use SoftDeletes;
    protected $table = 'mailer_contacts';

    protected $fillable = [
        'name', 'description', 'user_id'
    ];

    public function contacts() {
        return $this->belongsToMany(ClientsContact::class, 'mailer_contacts_lists_contacts', 'list_id', 'contact_id');
    }

    public function getContactsCount()
    {
        return ClientsContact::select('id', 'c.client_name as client_name', 'name', 'phone', 'c.client_id as cl_id', 'email')
            ->leftJoin('clients as c', 'clients_contact.client_id', '=', 'c.client_id')
            ->whereIn('id', $this->id)
            ->get();

    }
}
