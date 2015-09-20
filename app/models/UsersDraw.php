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

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->d_id;
        $data['payment'] = $this->d_payment;
        $data['account'] = $this->d_account;
        $data['amount'] = $this->d_amount;
        $date = new DateTime($this->created_at);
        $data['draw_at'] = $date->format('Y-m-d H:i:s');
        if (!empty($this->bank)) {
            $data['bank'] = $this->bank->showInList();
            $data['holder'] = $this->b_holder_name;
        }
        $data['status'] = $this->d_status;
        return $data;
    }

    public function addDraw()
    {
        $this->baseValidate();
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->d_status = 0;

        $this->save();

        return $this->d_id;
    }

    // relations
    public function bank()
    {
        return $this->hasOne('DicBank', 'b_id', 'b_id');
    }
}
