<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class TourismPlace extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id', 'name', 'description', 'adult_price', 'child_price', 'infant_price', 'tourist_price', 'longitude', 'latitude', 'facilities', 'status'
    ];

    /**
    * One to Many relationships
    */
    public function event()
    {
        return $this->hasMany(Event::class);
    }

    public function picture()
    {
        return $this->hasMany(Picture::class);
    }

    public function plan()
    {
        return $this->hasMany(Plan::class);
    }

    /**
    * Belongs To Relation
    */
    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }
}