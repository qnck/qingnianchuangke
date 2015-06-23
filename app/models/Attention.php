<?php

/**
*
*/

class Attention extends Eloquent
{
    public function followers()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function followings()
    {
        return $this->belongsTo('User', 'u_fans_id', 'u_id');
    }
}
