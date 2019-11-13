<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    //
    protected $primaryKey = 'user_id';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'channel_id',
        'group_id'
    ];
    
}
