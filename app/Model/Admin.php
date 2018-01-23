<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /**
    * Table database
    */
    protected $table = 'admins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'realname','email', 'web_token', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'web_token','created_at','updated_at'
    ];
}
