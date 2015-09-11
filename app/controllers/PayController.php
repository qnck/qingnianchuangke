<?php
/**
*
*/
class PayController extends \BaseController
{
    public function callbackAlipay()
    {
        $order_no = Input::get('out_trade_no', 0);
        $ali_trade_no = Input::get('trade_no', '');
        $ali_trade_status = Input::get('trade_status', '');
        $amount = Input::get('total_fee', 0);

        DB::beginTransaction();
        try {
            $alipay = new Alipay();
            $alipay->verifyNotify();

            $order = Order::where('o_number', '=', $order_no)->first();
            if (empty($order)) {
                throw new Exception("没有找到订单", 9002);
            }

            if ($ali_trade_status == 'TRADE_FINISHED' || $ali_trade_status == 'TRADE_SUCCESS') {
                $order->pay($amount, Alipay::PAYMENT_TAG);
                $carts = Cart::where('o_id', '=', $order->o_id)->where('c_status', '=', 2)->get();
                foreach ($carts as $key => $cart) {
                    $cart->checkout();
                }
            }
            DB::commit();
            echo "success";
        } catch (Exception $e) {
            DB::rollback();
            echo "fail";
        }
        exit;
    }
}
