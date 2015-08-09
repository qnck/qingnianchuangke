<?php
/**
*
*/
class Order extends Eloquent
{
    public $primaryKey = 'o_id';
    public $timestamps = false;

    public static $SHIPPING_STATUS_PREPARE = 1;
    public static $SHIPPING_STATUS_DELIVERING = 5;
    public static $SHIPPING_STATUS_FINISHED = 10;
    
    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'amount_origin' => $this->o_amount_origin, 'amount' => $this->o_amount, 'shpping_name' => $this->o_shipping_name, 'shpping_phone' => $this->o_shipping_phone, 'shpping_address' => $this->o_shipping_address, 'delivery_time' => $this->o_delivery_time, 'shpping' => $this->o_shipping, 'payment' => $this->o_payment, 'number' => $this->o_number],
            ['user' => 'required', 'amount_origin' => 'required', 'amount' => 'required', 'shpping_name' => 'required', 'shpping_phone' => 'required', 'shpping_address' => 'required', 'delivery_time' => 'required', 'shpping' => 'required', 'payment' => 'sometime', 'number' => 'required']
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
        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->o_id;
        $data['amount_origin'] = $this->o_amount_origin;
        $data['amount'] = $this->o_amount;
        $data['amount_paied'] = $this->o_amount_paied;
        $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
        $data['status'] = $this->o_status;
        $data['shipping_status'] = $this->o_shipping_status;
        $data['number'] = $this->o_number;

        if (!empty($this->carts)) {
            $carts = [];
            foreach ($this->carts as $key => $cart) {
                $carts[] = $cart->showInList();
            }
            $data['carts'] = $carts;
        }
        return $data;
    }

    public function addOrder()
    {
        $now = new DateTime();
        $this->o_status = 1;
        $this->o_shipping_status = Order::$SHIPPING_STATUS_PREPARE;
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->o_id;
    }

    public static function generateOrderNo($u_id)
    {
        $timestamp = time();
        $part1 = substr($timestamp, -6, 6);

        $len = strlen($u_id);
        if ($len < 6) {
            $part2 = $u_id;
            for ($i=0; $i < 6 - strlen($u_id); $i++) {
                $part2 = '0'.$part2;
            }
        } else {
            $part2 = substr($u_id, -6, 6);
        }

        $part3 = rand(100, 999);

        return '1'.$part1.$part2.$part3;
    }

    public static function updateShippingStatus($order_ids, $status)
    {
        if (!is_array($order_ids) || empty($order_ids)) {
            throw new Exception("无效的订单数据", 7001);
        }
        $re = Order::whereIn('o_id', $order_ids)->update(['o_shipping_status' => $status]);
        return $re;
    }

    public function pay()
    {
        
    }

    // laravel relation
    
    public function carts()
    {
        return $this->hasMany('Cart', 'o_id', 'o_id');
    }
}
