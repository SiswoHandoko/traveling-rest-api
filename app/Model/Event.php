<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
    * Table database
    */
    protected $table = 'events';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tp_id', 'name', 'description', 'start_date', 'end_date', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at'
    ];

    /**
    * Belongs To Relation
    */
    public function tourismplace() {
        return $this->belongsTo(TourismPlace::class, 'tp_id');
    }
}
