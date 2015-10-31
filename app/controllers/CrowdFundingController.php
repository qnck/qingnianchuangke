<?php
/**
*
*/
class CrowdFundingController extends \BaseController
{

    public function getCate()
    {
        $data = CrowdFunding::getCrowdFundingCate();
        $re = Tools::reTrue('获取分类成功', $data);
        return Response::json($re);
    }

    public function listCrowdFunding()
    {
        $per_page = Input::get('per_page', 30);

        $cate = Input::get('cate', 0);

        $range = Input::get('range', 1);
        $city = Input::get('city', 0);
        $school = Input::get('school', 0);

        try {
            $query = CrowdFunding::with(['city', 'school', 'user', 'product'])->where('c_status', '>', 2);
            if ($cate) {
                $query = $query->where('c_cate', '=', $cate);
            }
            if ($city && $range == 2) {
                $query = $query->where('c_id', '=', $city);
            }
            if ($school && $range = 3) {
                $query = $query->where('s_id', '=', $school);
            }
            $list = $query->paginate($per_page);
            $data = [];
            foreach ($list as $key => $funding) {
                $data[] = $funding->showInList();
            }
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取众筹失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getCrowdFunding($id)
    {
        try {
            $crowdfunding = CrowdFunding::find($id);
            $crowdfunding->load(['replies']);
            $data = $crowdfunding->showDetail();
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse('获取众筹失败');
        }
        return Response::json($re);
    }

    public function postReply($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $to = Input::get('to', 0);
        $to_u_id = Input::get('to_u_id', 0);
        $content = Input::get('content', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $to_user = User::find($to_u_id);
            if (empty($to_user)) {
                $to_u_id = 0;
                $to_u_name = '';
            } else {
                $to_u_name = $to_user->u_nickname;
            }
            $cf = CrowdFunding::find($id);
            $reply = [
                'to_id' => $to,
                'created_at' => Tools::getNow(),
                'content' => $content,
                'u_id' => $u_id,
                'u_name' => $user->u_nickname,
                'status' => 1,
                'to_u_id' => $to_u_id,
                'to_u_name' => $to_u_name,
            ];
            $replyObj = new Reply($reply);
            $cf->replies()->save($replyObj);
            $re = Tools::reTrue('回复成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '回复失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postOrder($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        
        $p_id = Input::get('product', 0);
        $quantity = Input::get('quantity', 0);

        $shipping_name = Input::get('shipping_name', '');
        $shipping_phone = Input::get('shipping_phone', '');
        $shipping_address = Input::get('shipping_address', '');
        $comment = Input::get('comment', '');

        DB::beginTransaction();
        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = CrowdFundingProduct::find($p_id);
            $funding = CrowdFunding::find($id);

            // add cart
            $cart = new Cart();
            $cart->p_id = $p_id;
            $cart->p_name = $product->p_title;
            $cart->u_id = $u_id;
            $cart->b_id = $product->b_id;
            $cart->created_at = Tools::getNow();
            $cart->c_quantity = $quantity;
            $cart->c_price = $product->p_price;
            $cart->c_amount = $product->p_price * $quantity;
            $cart->c_discount = 100;
            $cart->c_price_origin = $product->p_price;
            $cart->c_amount_origin = $product->p_price * $quantity;
            $cart->c_status = 2;
            $cart->c_type = 2;
            $cart->save();
            $product->loadProduct($quantity);
            if (!$funding->c_shipping) {
                $shipping_address = '';
                $shipping_name = $user->u_name;
                $shipping_phone = $user->u_mobile;
            }

            $date_obj = new DateTime($funding->active_at);
            $delivery_time_obj = $date_obj->modify('+'.($funding->c_time+$funding->c_yield_time).'days');

            // add order
            $order_group_no = Order::generateOrderGroupNo($u_id);
            $rnd_str = rand(10, 99);
            $order_no = $order_group_no.$cart->b_id.$rnd_str;
            $order = new Order();
            $order->u_id = $u_id;
            $order->b_id = $cart->b_id;
            $order->o_amount_origin = $cart->c_amount_origin;
            $order->o_amount = $cart->c_amount;
            $order->o_shipping_fee = $funding->c_shipping_fee;
            $order->o_shipping_name = $shipping_name;
            $order->o_shipping_phone = $shipping_phone;
            $order->o_shipping_address = $shipping_address;
            $order->o_delivery_time = $delivery_time_obj->format('Y-m-d H:i:s');
            $order->o_shipping = $funding->c_shipping;
            $order->o_comment = $comment;
            $order->o_number = $order_no;
            $order->o_group_number = $order_group_no;
            $o_id = $order->addOrder();
            Cart::bindOrder([$order->o_id => [$cart->c_id]]);

            // push msg to seller
            $booth = Booth::find($cart->b_id);
            $msg = new PushMessage($booth->u_id);
            $msg->pushMessage('您有新的订单, 请及时发货');
            $re = Tools::reTrue('提交订单成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '提交订单失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }
}
