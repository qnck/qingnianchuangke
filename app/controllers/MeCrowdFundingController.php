<?php
/**
*
*/
class MeCrowdFundingController extends \BaseController
{
    public function postCrowdFunding()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $title = Input::get('title', '');
        $cate = Input::get('cate', 1);
        $amount = Input::get('amount', 0);
        $time = Input::get('time', 0);
        $yield_time = Input::get('yield_time', 0);
        $shipping = Input::get('shipping', 0);
        $shipping_fee = Input::get('shipping_fee', 0);
        $brief = Input::get('brief', '');
        $yield_desc = Input::get('yield_desc', '');
        $content = Input::get('content', '');
        $open_file = Input::get('open_file', 0);
        $active_at = Input::get('active_at');
        $local_only = Input::get('local_only', 0);
        $is_schedule = Input::get('is_schedule', 0);

        if (empty($active_at)) {
            $active_at = Tools::getNow();
        }

        $mobile = Input::get('mobile', '');

        $price = Input::get('price', 0);
        $quantity = Input::get('quantity', 0);
        $is_limit = Input::get('is_limit', 0);

        $img_token = Input::get('img_token', '');
        $img_token_2 = Input::get('img_token_2', '');
        $apartment_no = Input::get('apartment_no', '');

        $content = urldecode($content);

        DB::beginTransaction();
        try {
            $user = User::chkUserByToken($token, $u_id);
            if (!$user->u_mobile) {
                throw new Exception("发布筹需要绑定联系电话，请到[我的-编辑资料]里绑定", 2001);
            }
            if ($cate == 8 && $user->u_type != 2) {
                throw new Exception("错误的发布类型", 2001);
            }

            $base = new DateTime();
            $base->modify('+90 days');
            $target = new DateTime($active_at);
            if ($base < $target) {
                throw new Exception("最大预发布时间不能超过3个月", 2001);
            }
            $user->load('booth', 'profileBase', 'school');
            if ($apartment_no) {
                $tmp_base = TmpUserProfileBase::find($user->u_id);
                $tmp_base->u_apartment_no = $apartment_no;
                $tmp_base->save();
            }
            if (empty($user->booth)) {
                $booth = new Booth();
                $booth->u_id = $u_id;
                $booth->b_type = 7;
                $booth->c_id = $user->school->t_city;
                $booth->s_id = $user->school->t_id;
                $booth->b_with_fund = 0;
                $booth->latitude = $user->latitude;
                $booth->longitude = $user->longitude;
                $booth->save();
            } else {
                $booth = $user->booth;
            }

            // add event
            $event = new EventItem();
            $event->e_title = $title;
            $event->e_range = 0;
            $event->e_brief = $brief;
            $event->e_start_at = $active_at;
            $date_obj = new DateTime($active_at);
            $date_obj->modify('+'.$time.' days');
            $event->e_end_at = $date_obj->format('Y-m-d H:i:s');
            $event->addEvent();

            $range = new EventRange();
            $range->e_id = $event->e_id;
            $range->s_id = $user->school->t_id;
            $range->c_id = $user->school->t_city;
            $range->p_id = $user->school->t_province;
            $range->save();
            
            // add funding
            $crowd_funding = new CrowdFunding();
            $crowd_funding->u_id = $u_id;
            $crowd_funding->b_id = $booth->b_id;
            $crowd_funding->e_id = $event->e_id;
            $crowd_funding->c_is_schedule = $is_schedule;
            $crowd_funding->c_yield_desc = $yield_desc;
            $crowd_funding->c_content = $content;
            $crowd_funding->c_yield_time = $yield_time;
            $crowd_funding->u_mobile = $mobile;
            $crowd_funding->c_time = $time;
            $crowd_funding->c_shipping = $shipping;
            $crowd_funding->c_shipping_fee = $shipping_fee;
            $crowd_funding->c_target_amount = $amount;
            $crowd_funding->c_cate = $cate;
            $crowd_funding->c_local_only = $local_only;
            $crowd_funding->c_open_file = $open_file;
            if ((int)$amount <= 50000) {
                $crowd_funding->c_status = 4;
            } else {
                $crowd_funding->c_status = 1;
                $msg = new MessageDispatcher($u_id);
                $msg->fireTextToUser('你此次众筹总金额已超过50000元，我们将在24小时以内进行审核，请耐心等待。');
            }

            // if the user is an official user, set funding type to offical
            if ($user->u_type == 2) {
                $crowd_funding->c_cate = 8;
            }
            $crowd_funding->addCrowdFunding();

            if ($img_token) {
                $imgObj = new Img('event', $img_token);
                $event->cover_img = $imgObj->getSavedImg($event->e_id);
                $event->save();
            }
            if ($img_token_2) {
                $imgObj = new Img('crowd_funding', $img_token_2);
                $crowd_funding->c_imgs = $imgObj->getSavedImg($crowd_funding->cf_id);
                $crowd_funding->save();
            }

            // add funding product
            $funding_product = new CrowdFundingProduct();
            $funding_product->cf_id = $crowd_funding->cf_id;
            $funding_product->u_id = $u_id;
            $funding_product->b_id = $booth->b_id;
            $funding_product->p_title = $title;
            $funding_product->p_desc = '';
            $funding_product->p_price = $price;
            $funding_product->p_target_quantity = $quantity;
            $funding_product->p_sort = 0;
            if ($is_limit) {
                $funding_product->p_max_quantity = $quantity;
            } else {
                $funding_product->p_max_quantity = 0;
            }

            $funding_product->addProduct();
            $re = Tools::reTrue('添加众筹成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '添加众筹失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }

    public function putCrowdFunding($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $title = Input::get('title', '');
        $cate = Input::get('cate', 1);
        $amount = Input::get('amount', 0);
        $time = Input::get('time', 0);
        $yield_time = Input::get('yield_time', 0);
        $shipping = Input::get('shipping', 0);
        $shipping_fee = Input::get('shipping_fee', 0);
        $brief = Input::get('brief', '');
        $yield_desc = Input::get('yield_desc', '');
        $content = Input::get('content', '');
        $open_file = Input::get('open_file', 0);
        $local_only = Input::get('local_only', 0);
        $is_schedule = Input::get('is_schedule', 0);
        $active_at = Input::get('active_at');
        if (empty($active_at)) {
            $active_at = Tools::getNow();
        }

        $mobile = Input::get('mobile', '');

        $price = Input::get('price', 0);
        $quantity = Input::get('quantity', 0);
        $is_limit = Input::get('is_limit', 0);

        $img_token = Input::get('img_token', '');
        $img_token_2 = Input::get('img_token_2', '');
        $crowd_img_1 = Input::get('crowd_img_1', '');
        $crowd_img_2 = Input::get('crowd_img_2', '');
        $crowd_img_3 = Input::get('crowd_img_3', '');
        $crowd_img_4 = Input::get('crowd_img_4', '');
        $crowd_img_5 = Input::get('crowd_img_5', '');
        $crowd_img_6 = Input::get('crowd_img_6', '');

        $deleted_img = Input::get('deleted_img', '');

        $apartment_no = Input::get('apartment_no', '');

        $content = urldecode($content);

        $img_array = [
            ['crowd_img_1' => $crowd_img_1],
            ['crowd_img_2' => $crowd_img_2],
            ['crowd_img_3' => $crowd_img_3],
            ['crowd_img_4' => $crowd_img_4],
            ['crowd_img_5' => $crowd_img_5],
            ['crowd_img_6' => $crowd_img_6],
        ];

        try {
            $user = User::chkUserByToken($token, $u_id);
            if ($cate == 8 && $user->u_type != 2) {
                throw new Exception("错误的发布类型", 2001);
            }

            $crowd_funding = CrowdFunding::find($id);
            if (empty($crowd_funding) || $crowd_funding->u_id != $u_id) {
                throw new Exception("无法获取到请求的众筹", 2001);
            }

            $funding_product = CrowdFundingProduct::where('cf_id', '=', $crowd_funding->cf_id)->first();
            if (empty($funding_product)) {
                throw new Exception("库存信息丢失", 2001);
            }
            if (Cart::getCartTypeCount(Cart::$TYPE_CROWD_FUNDING, $funding_product->p_id)) {
                throw new Exception("已有人购买", 2001);
            }

            if ($apartment_no) {
                $tmp_base = TmpUserProfileBase::find($user->u_id);
                $tmp_base->u_apartment_no = $apartment_no;
                $tmp_base->save();
            }

            $event = EventItem::find($crowd_funding->e_id);

            // put funding
            $event->e_title = $title;
            $event->e_brief = $brief;
            $event->e_start_at = $active_at;
            $date_obj = new DateTime($active_at);
            $date_obj->modify('+'.$time.' days');
            $event->e_end_at = $date_obj->format('Y-m-d H:i:s');

            $crowd_funding->c_local_only = $local_only;
            $crowd_funding->c_yield_desc = $yield_desc;
            $crowd_funding->c_content = $content;
            $crowd_funding->c_yield_time = $yield_time;
            $crowd_funding->u_mobile = $mobile;
            $crowd_funding->c_is_schedule = $is_schedule;
            $crowd_funding->c_time = $time;
            $crowd_funding->c_shipping = $shipping;
            $crowd_funding->c_shipping_fee = $shipping_fee;
            $crowd_funding->c_target_amount = $amount;
            $crowd_funding->c_cate = $cate;
            $crowd_funding->c_open_file = $open_file;
            if ($amount <= 2000) {
                $crowd_funding->c_status = 4;
            } else {
                $crowd_funding->c_status = 1;
            }

            // if the user is an official user, set funding type to offical
            if ($user->u_type == 2) {
                $crowd_funding->c_cate = 8;
            }

            $c_imgs = explode(',', $crowd_funding->c_imgs);
            if ($deleted_img) {
                $deleted_img = explode(',', $deleted_img);
                $imgObj= new Img('crowd_funding', '');
                $imgObj->batchRemove($crowd_funding->cf_id, $deleted_img);
                $c_imgs = array_diff($c_imgs, $deleted_img);
            }
            if ($img_array) {
                $img_obj = new Img('crowd_funding', $img_token_2);
                foreach ($img_array as $key => $img) {
                    if ($img) {
                        $old_key = Img::getKey($img);
                        if ($old_key != $key) {
                            $c_imgs = array_diff($c_imgs, [$img]);
                            $tmp = explode('/', $img);
                            $last_part = array_pop($tmp);
                            $tmp = explode('.', $last_part);
                            unset($tmp[0]);
                            $new = implode('.', $tmp);
                            $new_name = $key.'.'.$new;
                            $length = strlen($new_name);
                            $pos = strpos($tmp, $old_key);
                            $new_path = substr_replace($img, $new_name, $pos, $length);
                            $img_obj->replace($crowd_funding->cf_id, $img, $new_path);
                        }
                    }
                }
                $c_imgs = $imgObj->getSavedImg($crowd_funding->cf_id, implode(',', $c_imgs), true);
            }
            $crowd_funding->c_imgs = implode(',', $c_imgs);

            if ($img_token) {
                $img_obj = new Img('event', $img_token);
                $cover_img = $img_obj->transfer($event->e_id, 'cover_img');
                $event->cover_img = $cover_img;
            }
            $crowd_funding->save();
            $event->save();

            // put funding product
            $funding_product = CrowdFundingProduct::where('cf_id', '=', $crowd_funding->cf_id)->first();
            if (empty($funding_product)) {
                throw new Exception("库存信息丢失", 2001);
            }
            $funding_product->p_price = $price;
            $funding_product->p_target_quantity = $quantity;
            if ($is_limit) {
                $funding_product->p_max_quantity = $quantity;
            } else {
                $funding_product->p_max_quantity = 0;
            }
            $funding_product->save();
            $re = Tools::reTrue('编辑众筹成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '编辑众筹失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function delCrowdFunding($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        DB::beginTransaction();
        try {
            $user = User::chkUserByToken($token, $u_id);
            $crowd_funding = CrowdFunding::find($id);
            if (empty($crowd_funding) || $crowd_funding->u_id != $u_id) {
                throw new Exception("无法获取到请求的众筹", 2001);
            }
            
            $crowd_funding->delCrowdFunding();
            $re = Tools::reTrue('删除众筹成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '删除众筹失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }

    public function listSellCrowdFunding()
    {
        $per_page = Input::get('per_page', 30);
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $filter_option = Input::get('filter_option', 0);

        try {
            $now = Tools::getNow();
            $user = User::chkUserByToken($token, $u_id);
            $query = CrowdFunding::with([
                'city',
                'school',
                'user',
                'product',
                'eventItem',
                'praises' => function ($q) {
                    $q->where('praises.u_id', '=', $this->u_id);
                }
            ])->join('event_items', function ($q) {
                $q->on('event_items.e_id', '=', 'crowd_fundings.e_id');
            })->where('crowd_fundings.u_id', '=', $u_id);

            switch ($filter_option) {
                case 1:
                    $query = $query->where('crowd_fundings.c_status', '=', 1);
                    break;
                case 2:
                    $query = $query->where('crowd_fundings.c_status', '=', 2);
                    break;
                case 3:
                    $query = $query->where('event_items.e_start_at', '>', $now);
                    break;
                case 4:
                    $query = $query->where('crowd_fundings.c_status', '=', 4);
                    break;
                case 5:
                    $query = $query->where('crowd_fundings.c_status', '=', 5);
                    break;
                case 6:
                    $query = $query->where('crowd_fundings.c_status', '=', 3);
                    break;

                default:
                    break;
            }

            $list = $query->orderBy('crowd_fundings.created_at', 'DESC')->paginate($per_page);
            $data = [];
            foreach ($list as $key => $funding) {
                $tmp = $funding->showInList();
                $tmp['is_praised'] = 0;
                if (count($funding->praises) > 0) {
                    $tmp['is_praised'] = 1;
                }
                $data[] = $tmp;
            }
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取众筹失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listBuyCrowdFunding()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $per_page = Input::get('per_page', 30);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $query = CrowdFunding::select('crowd_fundings.*')->with([
                'city',
                'school',
                'user',
                'product',
                'eventItem',
                'praises' => function ($q) {
                    $q->where('praises.u_id', '=', $this->u_id);
                }
                ])
            ->join('crowd_funding_products', function ($q) {
                $q->on('crowd_fundings.cf_id', '=', 'crowd_funding_products.cf_id');
            })
            ->join('carts', function ($q) {
                $q->on('carts.p_id', '=', 'crowd_funding_products.p_id')->where('carts.c_type', '=', 2)->where('carts.u_id', '=', $this->u_id)->where('carts.c_status', '=', 3);
            });
            $list = $query->orderBy('created_at', 'DESC')->paginate($per_page);
            $data = [];
            foreach ($list as $key => $funding) {
                $tmp = $funding->showInList();
                $tmp['is_praised'] = 0;
                if (count($funding->praises) > 0) {
                    $tmp['is_praised'] = 1;
                }
                $data[] = $tmp;
            }
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取众筹失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
