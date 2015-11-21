<?php
/**
*
*/
class Event extends Eloquent
{
    public $primaryKey = 'e_id';
    public $timestamps = false;

    public function addEvent()
    {
        $this->created_at = Tools::getNow();
        $this->e_status = 1;
        return $this-save();
    }

    public function showInList()
    {
        $data = [];
        return $data;
    }
}
