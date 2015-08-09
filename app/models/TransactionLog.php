<?php
/**
*
*/
class TransactionLog extends Eloqunet
{
    public $primaryKey = 'l_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['mobile' => $this->_mobile],
            ['mobile' => 'required|digits:11']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addLog()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');

    }

    public function in()
    {

    }

    public function out()
    {

    }

    public function re()
    {

    }
}
