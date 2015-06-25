<?php
/**
*
*/
class DicBank extends Eloquent
{
    public $primaryKey = 'b_id';
    public $timestamps = false;

    public function cards()
    {
        return $this->hasMany('UsersBankCard', 'b_id', 'b_id');
    }
}
