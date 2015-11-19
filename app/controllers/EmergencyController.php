<?php
/**
* 
*/
class EmergencyController extends \BaseController
{
    public function sendOrders()
    {
        set_time_limit(60000);
        try {
            $user = User::find(5);
            if ($user->u_priase_count == 0) {
                throw new Exception("已经执行过了", 30001);
            } else {
                $user->u_priase_count = 0;
                $user->save();
            }
            $str_text = '恭喜“双11不怕剁手”众筹活动已成功，您被众筹发起者选中，请于12日18时前凭此信息到零栋铺子领取众筹回报。4006680550';
            $str_push = '恭喜“双11不怕剁手”众筹活动已成功，您被众筹发起者选中，请于12日18时前凭此信息到零栋铺子领取众筹回报。4006680550';

            $orders = Order::selectRaw('right(`t_orders`.`o_number`, 4) AS seed, `t_orders`.*')
            ->join('carts', function ($q) {
                $q->on('orders.o_id', '=', 'carts.o_id');
            })->where('carts.c_type', '=', 2)->where('carts.p_id', '=', 4)->orderBy('seed', 'DESC')->limit(111)->get();
            
            foreach ($orders as $key => $order) {
                if (empty($order)) {
                    continue;
                }
                $phones = $order->o_shipping_phone;
                $pushObj = new PushMessage($order->u_id);
                $pushObj->pushMessage($str_push);
                echo 'pushed to '.$order->u_id.' </br>';
                $phoneObj = new Phone($order->o_shipping_phone);
                $phoneObj->sendText($str_text);
                echo 'texted to '.$order->o_shipping_phone.' </br>';
            }

            File::put('/var/www/qingnianchuangke/phones', implode(',', $phones));

            $re = Tools::reTrue('发送中奖信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '发送中奖信息失败:'.$e->getMessage());
        }

        return Response::json($re);
    }

    public function countUsers()
    {
        $count = DB::table('users')->count();
        echo "当前注册人数:".$count.'人';
        exit();
    }

    public function test()
    {
        $str = '恭喜“双11不怕剁手”众筹活动已成功，您被众筹发起者选中，请于12日18时前凭此信息到零栋铺子领取众筹回报。4006680550';
        $phone = new Phone('18508237273');
        $re = $phone->sendText($str);
        var_dump($re);
    }
}
