<?php
/**
*
*/
class PushMessage extends Eloquent
{
    public $timestamps = false;

    private $_u_id = 0;

    public function __construct($u_id)
    {
        $this->_u_id = $u_id;
    }

    // public function 
}
