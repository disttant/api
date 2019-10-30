<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{



    public    $timestamps = false;
    protected $fillable = [
        'sandbox',
        'channel'
    ];



    /* *
     *  Creates a new channel into the given home
     *
     * */
    public static function List(string $sandbox = null, int $page = 1, int $perPage = 10)
    {
        if ( is_null($sandbox) || empty($sandbox) )
            return [];

        return Channel::select('channel')
            ->where('sandbox', $sandbox)
            ->forPage($page, $perPage)
            ->get();
    }



    /* *
     *  Creates a new channel into the given home
     *
     * */
    public static function Free(string $sandbox = null, int $page = 1, int $perPage = 10)
    {
        if ( is_null($sandbox) || empty($sandbox) )
            return [];

        return Channel::select('channel')
            ->where('sandbox', $sandbox)
            ->whereNotIn('id', 
                Relation::select('channel_id')
                    ->whereColumn('channel_id', 'channels.id')
                    ->where('sandbox', $sandbox)
            )
            ->forPage($page, $perPage)
            ->get();

    }



    /* *
     *  Creates a new channel into the given home
     *
     * */
    public static function Create(string $sandbox = null, string $name = null)
    {
        if ( is_null($sandbox) || empty($sandbox) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $channel = Channel::firstOrNew([
            'sandbox' => $sandbox,
            'channel' => $name
        ]);

        $channel->save();

    }



    /* *
     * Retrieves N messages for a given home-channel pair
     *
     * */
    public static function Messages(string $sandbox = null, string $channel = null, $limit = 10)
    {
        if ( is_null($sandbox) || empty($sandbox) )
            return [];

        if ( is_null($channel) || empty($channel) )
            return [];

        return Message::
              select('message', 'created_at')
            ->orderByDesc(
                Channel::select('channel')
                    ->whereColumn('channel_id', 'channels.id')
                    ->where('channel', $channel)
                    ->where('sandbox', $sandbox)
                    ->orderBy('id', 'desc')
                    ->limit(1)
            )
            ->where('sandbox', $sandbox)
            ->limit($limit, 10)
            ->get();
    }



    
}
