<?php
/**
*
*/
class Cart extends Eloquent
{
    public $primaryKey = 'c_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['booth' => $this->b_id, 'user' => $this->u_id, 'product' => $this->p_id, 'price' => $this->c_price_origin, 'quntity' => $this->c_quntity, 'discount' => $this->c_discount],
            ['booth' => 'required', 'user' => 'required', 'product' => 'required', 'price' => 'required', 'quntity' => 'required', 'discount' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addCart()
    {
        $this->baseValidate();
        $this->c_price = $this->c_price_origin * $this->discount /100;
        $this->c_amount_origin = $this->c_price_origin * $this->c_quntity;
        $this->c_amount = $this->c_amount_origin * $this->c_discount /100;

    }
}
