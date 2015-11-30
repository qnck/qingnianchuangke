<?php
/**
* 
*/
use \Illuminate\Filesystem\Filesystem;

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

    public function fakeUser()
    {
        $batch = Input::get('batch', 0);
        if (!$batch) {
            echo "need batch number";
            die();
        }
        $mobile = DB::table('users')->select('u_mobile')->where('u_mobile', '<', '12000000000')->orderBy('u_mobile', 'DESC')->first();
        if (!$mobile) {
            $mobile = 10000000000;
        } else {
            $mobile = $mobile->u_mobile;
        }
        echo "mobile start at ".$mobile."</br>";
        echo "batch number is ".$batch."</br>";
        set_time_limit(0);
        $file = new Filesystem();
        $re = $file->files('/tmp/qnck_fackuser');
        foreach ($re as $key => $path) {
            $user = new User;
            $user->u_mobile = $mobile++;
            $user->u_name = $user->u_nickname = $file->name($path);
            echo "add:".$user->u_name."</br>";
            $user->u_head_img = 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/user_head/'.$batch.'/'.($key+1).'.jpg';
            $user->fakeUser();
        }
        echo 'done';
    }

    public function winTheBid()
    {
        Auction::runTheWheel();
    }
}
