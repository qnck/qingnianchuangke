<?php
/**
*
*/
class SysUser extends \Eloquent
{
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['u_name'] = $this->u_name;
        $data['last_local'] = $this->last_local;
        $data['last_time'] = $this->last_time;
        $data['status'] = $this->status;
        $data['is_del'] = $this->is_del;
        
        return $data;
    }
}
