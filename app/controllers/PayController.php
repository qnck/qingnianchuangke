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

    public function wechatPayPreOrder()
    {
        $order_no = Input::get('order_no', '');
        $token = Input::get('token', '');
        $u_id = Input::get('u_d', 0);

        // try {
            $user = User::chkUserByToken($token, $u_id);
            $order = Order::getOrderByNo($order_no);
            $product_names = Cart::where('o_id', '=', $order->o_id)->lists('p_name');
            $wechat = new WechatPay();
            $body = $product_names[0].'等商品';
            $params = [
                'out_trade_no' => $order_no,
                'total_fee' => $order->o_amount,
                'body' => $body,
                'detail' => implode(',', $product_names)
            ];
            $re = $wechat->preOrder($params);
            var_dump($re);
        // } catch (Exception $e) {
            
        // }
    }
}
