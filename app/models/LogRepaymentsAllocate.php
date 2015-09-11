<?php
/**
*
*/
class LogRepaymentsAllocate extends Eloquent
{
    public $primaryKey = 'log_id';
    public $timestamps = false;

    public function addLog()
    {
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
    }
}
