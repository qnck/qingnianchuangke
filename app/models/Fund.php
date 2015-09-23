<?php
/**
*
*/
class Fund extends Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'amount' => $this->t_apply_money, 'profit_rate' => $this->t_profit_rate, 'booth' => $this->b_id],
            ['user' => 'required', 'amount' => 'required', 'profit_rate' => 'required|digits:2', 'booth' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->t_id;
        $data['profit'] = $this->t_profit_rate;
        $data['loan'] = $this->t_apply_money;

        if ($this->t_repay_start_date) {
            $startObj = new DateTime($this->t_repay_start_date);
            $start = $startObj->getTimestamp();
            $data['loan_start'] = $startObj->format('Y-m-d H:i:s');
        } else {
            $start = 0;
            $data['loan_start'] = null;
        }
        // if ($this->t_repay_end_date) {
        //     $endObj = new DateTime($this->t_repay_end_date);
        //     $end = $endObj->getTimestamp();
        //     $data['loan_end'] = $endObj->format('Y-m-d H:i:s');
        // } else {
        //     $end = 0;
        //     $data['loan_end'] = null;
        // }
        // $data['loan_period'] = ceil(($end - $start) / (3600 * 24));
        // 

        $data['status'] = $this->t_status;
        $loans = null;
        if (!empty($this->loans)) {
            foreach ($this->loans as $key => $loan) {
                $loans[] = $loan->showInList();
            }
        }
        $data['loans'] = $loans;
        $data['loan_period'] = count($loans);
        return $data;
    }

    public function addFund()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->t_id;
    }

    public function addCensorLog($content)
    {
        $log = new LogUserProfileCensors();
        $log->u_id = $this->u_id;
        $log->cate = 'fund';
        $log->content = $content;
        $log->admin_id = Tools::getAdminId();
        $log->addLog();
    }

    public function interview()
    {
        $old_status = '审核之前的状态为: '.$this->getOriginal('t_status').', 审核之后的状态为: '.$this->t_status.'.';
        if ($this->t_status == 1) {
            $content = '基金审核未通过, '.$old_status.' 备注: '.$this->remark;
        } elseif ($this->t_status == 3) {
            $content = '基金审核通过, '.$old_status;
        } else {
            $content = '审核基金记录, '.$old_status;
        }
        $pushMsgObj = new PushMessage($this->u_id);
        $pushMsgObj->pushMessage($content);
        $this->addCensorLog($content);
        return $this->save();
    }

    public function censorPass($interview)
    {
        if ($interview == 1) {
            $re = $this->censorPassNeedInterview();
        } else {
            $re = $this->censorPassNoInterview();
        }
        return $re;
    }

    public function censorPassNeedInterview()
    {
        $this->t_status = 2;
        return $this->save();
    }

    public function censorPassNoInterview()
    {
        $this->t_status = 3;
        return $this->save();
    }

    public function censorFailed()
    {
        $this->t_status = 1;
        return $this->save();
    }

    public function apply()
    {
        $this->t_status = 0;
        return $this->addFund();
    }

    public function getCurrentPeriodIncome()
    {
        $lastDate = $this->getLastRepayDate();
        $income = Cart::sumIncome($lastDate, null, $this->b_id);
        return $income;
    }

    public function getAllIncome()
    {

    }

    public function getLastRepayDate()
    {
        if (empty($this->loans)) {
            throw new Exception("该基金下没有借款", 6001);
        }
        $lastDate = null;
        foreach ($this->loans as $key => $loan) {
            if (!$loan->repaied_at) {
                continue;
            }
            $date = new DateTime($this->repaied_at);
            if ($lastDate == '') {
                $lastDate = $date;
            }
            if ($lastDate < $date) {
                $lastDate = $data;
            }
        }
        if ($lastDate) {
            $lastDate = $lastDate->format('Y-m-d');
        }
        return $lastDate;
    }

    public static function clearByUser($u_id)
    {
        $record = Fund::where('u_id', '=', $u_id)->where('t_status', '=', 0)->first();
        $f_id = 0;
        if (!empty($record)) {
            $f_id = $record->t_id;
            $record->delete();
        }
        return $f_id;
    }

    // laravel relations

    public function loans()
    {
        return $this->hasMany('Repayment', 'f_id', 't_id');
    }

    public function booth()
    {
        return $this->belongsTo('Booth', 'b_id', 'b_id');
    }
}
