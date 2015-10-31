<?php
/**
*
*/
class CrowdFundingProduct extends Eloquent
{
    public $primaryKey = 'p_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'booth' => $this->b_id],
            ['user' => 'required', 'booth' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addProduct()
    {
        $this->baseValidate();
        $now = Tools::getNow();
        $this->created_at = $now;
        $this->p_status = 1;
        return $this->save();
    }

    public function loadProduct($quantity)
    {
        $up = false;
        $limit = false;
        $re = false;
        if ($this->p_max_quantity > 0) {
            $remain = $this->p_max_quantity - $this->p_sold_quantity;
            if ($quantity < $remain) {
                $up = true;
                $limit = true;
            } else {
                throw new Exception("库存不足", 7006);
            }
        } else {
            $up = true;
        }
        $query = DB::table('crowd_funding_products')->where('p_id', '=', $this->p_id);
        if ($limit) {
            $query->where('p_max_quantity', '<=', '(p_sold_quantity + '.$quantity.')');
        }
        if ($up) {
            $re = $query->increment('p_sold_quantity', $quantity);
        } else {
            throw new Exception("修改库存失败", 7001);
        }
        return $re;
    }

    public function unloadProduct($quantity)
    {
        $down = false;
        $re = false;
        if ($quantity > $this->p_sold_quantity) {
            throw new Exception("最多还能退".$this->p_sold_quantity.'份', 7001);
        } else {
            $down = true;
        }
        $re = DB::table('crowd_funding_products')->where('p_id', '=', $this->p_id)->where('p_sold_quantity', '<=', $quantity)->decrement('p_sold_quantity', $quantity);
        return $re;
    }

    // relation
}
