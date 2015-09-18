<?php
/**
*
*/
class UsersDraw extends Eloquent
{
    public $primaryKey = 'd_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['account' => $this->d_account, 'amount' => $this->d_amount, 'payment' => $this->d_payment],
            ['account' => 'required', 'amount' => 'required', 'payment' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addDraw()
    {
        $this->baseValidate();
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->d_status = 0;

        return $this->save();
    }
}
