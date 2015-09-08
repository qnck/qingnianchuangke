<?php
/**
*
*/
class Repayment extends Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['fund' => $this->f_id, 'amount' => $this->f_re_money, 'schema' => $this->f_schema],
            ['fund' => 'required', 'amount' => 'required', 'schema' => 'required']
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
        $data['id'] = $this->t_id;
        $data['schema'] = $this->f_schema;
        $data['percentage'] = $this->f_percentage;
        $data['amount'] = $this->f_re_money;
        $data['status'] = $this->f_status;
        return $data;
    }

    public function addRepayment()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->id;
    }

    public function apply()
    {
        $this->f_status = 0;
        return $this->addRepayment();
    }

    public function allocate()
    {
        $this->f_status = 1;
        $fund = Fund::find($this->f_id);
        if (empty($fund)) {
            throw new Exception("没有找到相关的基金信息", 1001);
        }
        // do transaction
        $userBankCard = UsersBankCard::find('u_id', '=', $fund->u_id)->first();
        if (empty($userBankCard)) {
            throw new Exception("没有找到用户相关的银行卡信息", 10001);
        }
        
        return $this->save();
    }

    public function repayAll()
    {
        $this->f_status = 3;
        return $this->save();
    }

    public function repayPartial()
    {
        $this->f_status = 2;
        return $this->save();
    }

    public static function clearByFund($f_id)
    {
        $re = Repayment::where('f_id', '=', $f_id)->get();
        if (!empty($re)) {
            foreach ($re as $key => $rp) {
                $rp->delete();
            }
        }
    }
}
