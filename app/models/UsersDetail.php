<?php
/**
*
*/
class UsersDetail extends Eloquent
{
    public $primaryKey = 'u_id';
    public $timestamps = false;

    // laravel relations
    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}
