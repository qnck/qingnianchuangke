<?php
/**
*
*/
class DicCity extends \Eloquent
{
    public $primaryKey = 'c_name';
    public $timestamps = false;

    const VER = 1;

    public function showInList()
    {
        $data['id'] = $this->c_id;
        $data['name'] = $this->c_name;
        $data['province_id'] = $this->c_province_id;
        return $data;
    }
}
