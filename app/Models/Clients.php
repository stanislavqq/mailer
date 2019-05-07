<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Clients extends Model
{

    protected $table = 'clients';


    protected $dates = [
        'created_at',
        'updated_at',
    ];


    protected $fillable = [
        'client_id', 'client_name'
    ];

}