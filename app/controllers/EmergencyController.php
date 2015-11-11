<?php
/**
* 
*/
class EmergencyController extends \BaseController
{
    public function sendOrders()
    {
        set_time_limit(60000);

        $cf_id = Input::get('cf_id');

        $orders = Input::get('orders', '');

        $time_from = Input::get('time_from', '');
        $time_to = Input::get('time_to', '');
        $address = Input::get('address', '');
        $action = Input::get('action', '');

        try {
            $funding = CrowdFunding::find($cf_id);
            if (empty($funding)) {
                throw new Exception("没有找到请求的众筹", 2001);
            }

            $title = $funding->c_title;

            $str_text = '尊敬的用户，您好，您在【青年创】参加的众筹【'.$title.'】已众筹成功，恭喜您被众筹发起者选中，请于'.$time_from.'至'.$time_to.'内，前往['.$address.']处'.$action.'。';
            $str_push = '尊敬的用户，您好，您参加的众筹【'.$title.'】已众筹成功，恭喜您被众筹发起者选中，请于'.$time_from.'至'.$time_to.'内，前往['.$address.']处'.$action.'。';


            $o_ids = explode(',', $orders);
            foreach ($o_ids as $key => $o_id) {
                if (!is_numeric($o_id)) {
                    unset($o_ids[$key]);
                }
            }
            $o_ids = [];
            foreach ($o_ids as $key => $o_id) {
                $order = Order::find($o_id);
                if (empty($order)) {
                    continue;
                }
                $pushObj = new PushMessage($order->u_id);
                $pushObj->pushMessage($str_push);
                $phoneObj = new Phone($order->o_shipping_phone);
                $phoneObj->sendText($str_text);
            }
            $re = Tools::reTrue('发送中奖信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '发送中奖信息失败:'.$e->getMessage());
        }

        return Response::json($re);
    }

    public function test()
    {
        $str = '尊敬的用户，您好，您在【青年创】参加的众筹【悠悠川大情，心系老年人】已众筹成功，恭喜您被众筹发起者选中，请于2015-11-11 14:30:00至2015-11-13 14:30:00内，前往[成都市桐梓林北路中华园中苑3栋2单元A座]处领取回报。';
        $phone = new Phone('18508237273');
        $re = $phone->sendText($str);
        var_dump($re);
    }
}
