<?php
/**
*
*/
class EventRange extends Eloquent
{
    public $timestamps = false;
    protected $fillable = ['e_id', 's_id', 'c_id', 'p_id'];

    public function showInList()
    {
        $data = [];
        $data['s_id'] = $this->s_id;
        $data['c_id'] = $this->c_id;
        $data['p_id'] = $this->p_id;
        return $data;
    }
}
