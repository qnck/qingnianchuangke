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

    public static function clearByFund($f_id)
    {
        $re = Repayment::where('f_id', '=', $f_id)->get();
        foreach ($re as $key => $rp) {
            $rp->delete();
        }
    }
}
