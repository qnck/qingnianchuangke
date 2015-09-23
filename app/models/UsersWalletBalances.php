<?php
/**
*
*/
class UsersWalletBalances extends Eloquent
{
    public $primaryKey = 'u_id';
    public $timestamps = false;

    public function putIn($amount)
    {
        $this->w_balance += $amount;
        return $this->save();
    }

    public function getOut($amount)
    {
        $this->w_balance -= $amount;
        if ($this->w_balance < 0) {
            throw new Exception("账户没有足够多的余额", 9009);
        }
        return $this->save();
    }

    public function freez($amount)
    {
        $this->w_balance -= $amount;
        if ($this->w_balance < 0) {
            throw new Exception("无法冻结更多的余额", 9009);
        }
        $this->w_freez += $amount;
        return $this->save();
    }

    public function deFreez($amount)
    {
        $this->w_freez -= $amount;
        if ($this->w_freez < 0) {
            throw new Exception("无法解冻更多的资金", 1);
        }
        $this->w_balance += $amount;
        return $this->save();
    }
}
