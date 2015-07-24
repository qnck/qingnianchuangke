<?php
/**
*
*/
class DicBank extends Eloquent
{
    public $primaryKey = 'b_id';
    public $timestamps = false;

    const VER = 1;

    public function showInList()
    {
        $data = [];

        $data['name'] = $this->b_name;
        $data['logo'] = $this->b_logo;

        return $data;
    }
}
