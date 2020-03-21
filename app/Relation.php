<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{

    protected $primaryKey = 'user_id';
    
    public $incrementing = false;
    public $timestamps = false;


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'group_id',
    ];


    
}
