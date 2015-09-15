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

    public static $STATUS_INVALIDE = 0;
    public static $STATUS_UNFINISHED = 1;
    public static $STATUS_FINISHED = 2;
    public static $STATUS_PACKED = 3;
    public static $STATUS_SHIPPED = 4;
    public static $STATUS_ORDERED = 5;
    public static $STATUS_PAIED = 6;

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

    public function showDetail($mask_status = false)
    {
        $data = [];
        $data['id'] = $this->o_id;
        $data['amount_origin'] = $this->o_amount_origin;
        $data['amount'] = $this->o_amount;
        $data['amount_paied'] = $this->o_amount_paied;
        $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
        $data['number'] = $this->o_number;

        if ($mask_status) {
            $data['status'] = $this->mapOrderStatus();
        } else {
            $data['status'] = $this->o_status;
            $data['shipping_status'] = $this->o_shipping_status;
        }

        if (!empty($this->carts)) {
            $carts = [];
            foreach ($this->carts as $key => $cart) {
                $carts[] = $cart->showInList();
            }
            $data['carts'] = $carts;
        }
        return $data;
    }

    public function mapOrderStatus()
    {
        $invalide = false;
        $shipped = false;
        $paied = false;
        if (in_array($this->o_status, [0, 3])) {
            $invalide = true;
        } elseif ($this->o_status == 1) {
            $paied = false;
        } elseif ($this->o_status == 2) {
            $paied = true;
        }
        if (in_array($this->o_shipping_status, [1, 5])) {
            $shipped = false;
        } elseif ($this->o_shipping_status == 10) {
            $shipped = true;
        }

        if ($invalide) {
            return Order::$STATUS_INVALIDE;
        }
        if ($shipped && $paied) {
            return Order::$STATUS_FINISHED;
        }
        if (!$shipped) {
            return Order::$STATUS_PACKED;
        }
        if (!$paied) {
            return Order::$STATUS_ORDERED;
        }
        if ($shipped) {
            return Order::$STATUS_SHIPPED;
        }
        if ($paied) {
            return Order::$STATUS_PAIED;
        }
        return 1;
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
        $order_no = Tools::generateDateUserRandomNo($u_id);
        $chk = Order::where('o_number', '=', $order_no)->first();
        $count = 0;
        while (!empty($chk)) {
            $order_no = Tools::generateDateUserRandomNo($u_id);
            $chk = Order::where('o_number', '=', $order_no)->first();
            $count++;
            if ($count >= 10) {
                throw new Exception("无法生成订单号", 9001);
                break;
            }
        }
        return $order_no;
    }

    public static function updateShippingStatus($order_ids, $status)
    {
        if (!is_array($order_ids) || empty($order_ids)) {
            throw new Exception("无效的订单数据", 9002);
        }
        $re = Order::whereIn('o_id', $order_ids)->update(['o_shipping_status' => $status]);
        return $re;
    }

    public function pay($amount, $payment_type)
    {
        if ($this->o_status >= 2) {
            return true;
        }
        $now = new DateTime();
        $this->o_status = 2;
        $this->paied_at = $now->format('Y-m-d H:i:s');
        $this->o_amount_paied = $amount;
        $this->o_payment = $payment_type;
        if (!$this->save()) {
            throw new Exception("结算订单失败", 9004);
        }
        return true;
    }

    // laravel relation
    
    public function carts()
    {
        return $this->hasMany('Cart', 'o_id', 'o_id');
    }
}
