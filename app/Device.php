<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    #public    $timestamps = false;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'node_id'
    ];



}