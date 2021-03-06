<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{

    /**
    * Table database
    */
    protected $table = 'advertisements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_url', 'title', 'caption','type','status','city_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at','city_id'
    ];

    /**
    * Belongs To Relation
    */
    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }
}
