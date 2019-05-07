<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 24.05.2018
 * Time: 18:32
 */

namespace App\Models;


use App\Models\Mailer\ContactList;
use Illuminate\Database\Eloquent\Model;

class ClientsContact extends Model
{
    protected $table = 'clients_contact';
    protected $fillable = [
        'id',
        'name',
        'client_id',
        'position',
        'phone',
        'email',
        'skype',
        'notes'
    ];
    public static function getContact(){
        return self::all();
    }

    public function client() {
        return $this->belongsTo(Client::class, 'client_id', 'client_id')
            ->select('client_id', 'client_name');
    }

    public function contactLists() {
        return $this->belongsToMany(ContactList::class, 'mailer_contacts_lists_contacts', 'contact_id', 'id');
    }
}