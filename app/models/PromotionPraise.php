<?php
/**
*
*/
class PromotionPraise extends Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['promo' => $this->prom_id, 'user' => $this->u_id, 'name' => $this->u_name],
            ['promo' => 'required|digits_between:1,11', 'user' => 'required|digits_between:1,11', 'name' => 'required']
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
        $data['u_id'] = $this->u_id;
        $data['name'] = $this->u_name;
        return $data;
    }

    public function addPraise()
    {
        $praise = PromotionPraise::where('prom_id', '=', $this->prom_id)->where('u_id', '=', $this->u_id)->first();
        if (!empty($praise->t_id)) {
            throw new Exception("已经点过赞了", 7001);
        }
        $this->baseValidate();
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
    }

    // laravel relations

    public function promoInfo()
    {
        return $this->belonsTo('PromotionInfo', 'pormo_id', 'p_id');
    }
}
