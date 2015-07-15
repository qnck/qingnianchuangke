<?php

/**
*
*/
class DicSchool extends Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    public function showInList()
    {
        $data['id'] = $this->t_id;
        $data['school_name'] = $this->t_name;
        return $data;
    }
}
