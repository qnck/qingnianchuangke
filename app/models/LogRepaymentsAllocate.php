<?php
/**
*
*/
class LogRepaymentsAllocate extends Eloquent
{
    public $primaryKey = 'log_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['admin' => $this->admin_id, 'loan' => $this->repayment_id],
            ['admin' => 'required', 'loan' => 'required']
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
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
    }
}
