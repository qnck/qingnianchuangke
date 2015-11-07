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

            $orders = Order::getGroupOrdersByNo($order_no);
            if ($ali_trade_status == 'TRADE_FINISHED' || $ali_trade_status == 'TRADE_SUCCESS') {
                foreach ($orders as $key => $order) {
                    $order->pay(Alipay::PAYMENT_TAG);
                    $order->checkoutCarts();
                }
            }
            DB::commit();
            echo "success";
            die();
        } catch (Exception $e) {
            DB::rollback();
            echo "fail";
            die();
        }
        exit;
    }

    public function callbackWechat()
    {
        DB::beginTransaction();
        try {
            $wechat = new WechatPay();
            $wechat->log->INFO('callback start');
            $input = file_get_contents('php://input', 'r');
            $wechat->log->INFO('POSTED DATE FRON WX SERVER:'.$input);

            $re = $wechat->verifyNotify();
            $re['total_fee'] = $re['total_fee'] * 0.01;
            $orders = Order::getGroupOrdersByNo($re['out_trade_no']);
            foreach ($orders as $key => $order) {
                $order->pay(WechatPay::PAYMENT_TAG);
                $order->checkoutCarts();
            }
            $wechat->_notify->SetReturn_code('SUCCESS');
            $wechat->_notify->SetReturn_msg('OK');
            DB::commit();
        } catch (Exception $e) {
            $wechat->log->ERROR('exeception caught:'.$e->getMessage());
            $wechat->_notify->SetReturn_code('FAIL');
            $wechat->_notify->SetReturn_msg($e->getMessage());
            DB::rollback();
        }
        $re = $wechat->_notify->ToXml();
        $wechat->log->INFO('XML:'.$re);
        WxpayApi::replyNotify($re);
        exit;
    }

    public function wechatPayPreOrder()
    {
        $order_no = Input::get('order_no', '');
        $token = Input::get('token', '');
        $u_id = Input::get('u_d', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $order = Order::getOrderByNo($order_no);
            $product_names = Cart::where('o_id', '=', $order->o_id)->lists('p_name');
            $wechat = new WechatPay();
            $body = $product_names[0].'等商品';
            $params = [
                'out_trade_no' => $order_no,
                'total_fee' => $order->o_amount,
                'body' => $body,
                'detail' => implode(',', $product_names),
            ];
            $re = $wechat->preOrder($params);
            $re = Tools::reTrue('微信预支付成功', $re);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '微信预支付失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
