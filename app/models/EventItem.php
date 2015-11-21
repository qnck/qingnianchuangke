<?php
/**
*
*/
class EventItem extends Eloquent
{
    public $primaryKey = 'e_id';
    public $timestamps = false;

    public function addEvent()
    {
        $this->created_at = Tools::getNow();
        $this->e_status = 1;
        return $this->save();
    }

    public function showInList()
    {
        $data = [];
        return $data;
    }

    // relations
    public function ranges()
    {
        return $this->hasMany('EventRange', 'e_id', 'e_id');
    }

    public function positions()
    {
        return $this->hasMany('EventPosition', 'e_id', 'e_id');
    }
}
