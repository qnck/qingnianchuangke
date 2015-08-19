<?php
/**
*
*/
class MarketController extends \BaseController
{
    public function index()
    {
        $site = Input::get('site', 0);
        $school = Input::get('school', 0);
        $key = Input::get('key', '');
        $range = Input::get('range', 1);
        $u_id = Input::get('u_id');

        $page = Input::get('page', 0);
        $perPage = Input::get('per_page', 30);

        try {
            if (!$u_id) {
                throw new Exception("请传入有效的用户id", 2001);
            }
            $query = PromotionInfo::with([
                'school',
                'booth' => function ($q) {
                    $q->with(['user']);
                },
                'praises' => function ($q) {
                    $q->take(3);
                },
                'product' => function ($q) {
                    $q->with(['promo', 'quantity']);
                }]);
            $query = $query->select('promotion_infos.*')->where('promotion_infos.p_range', '=', $range);
            $query = $query->leftJoin('products', function ($q) {
                $q->on('products.p_id', '=', 'promotion_infos.p_id')
                ->where('products.p_status', '=', 1);
            })->leftJoin('booths', function ($q) {
                $q->on('booths.b_id', '=', 'promotion_infos.b_id')
                ->where('booths.b_status', '=', 1);
            });
            if ($school) {
                $query = $query->where('promotion_infos.s_id', '=', $school);
            }
            if ($site) {
                $query = $query->where('promotion_infos.c_id', '=', $site);
            }
            if ($key) {
                $query = $query->where(function ($q) use ($key) {
                    $q->where('promotion_infos.p_content', 'LIKE', '%'.$key.'%')
                    ->orWhere('booths.b_product_source', 'LIKE', '%'.$key.'%')
                    ->orWhere('booths.b_product_category', 'LIKE', '%'.$key.'%')
                    ->orWhere('booths.b_desc', 'LIKE', '%'.$key.'%')
                    ->orWhere('booths.b_title', 'LIKE', '%'.$key.'%')
                    ->orWhere('products.p_title', 'LIKE', '%'.$key.'%')
                    ->orWhere('products.p_desc', 'LIKE', '%'.$key.'%');
                });
            }
            $list = $query->orderBy('promotion_infos.created_at', 'DESC')->paginate($perPage);
            $data = [];
            $promo_ids = [];
            foreach ($list as $key => $product) {
                $data[] = $product->showInListWithProduct();
                $promo_ids[] = $product['p_id'];
            }
            if (!empty($promo_ids)) {
                $praises = PromotionPraise::where('u_id', '=', $u_id)->whereIn('prom_id', $promo_ids)->lists('prom_id');
            } else {
                $praises = [];
            }
            foreach ($data as $key => $product) {
                if (in_array($product['product']['id'], $praises)) {
                    $chk = 1;
                } else {
                    $chk = 0;
                }
                $product['is_praised'] = $chk;
                $data[$key] = $product;
            }
            $re = Tools::reTrue('获取首页商品成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取首页商品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function convenient()
    {
        $school = Input::get('school', 0);
        $key = Input::get('key', '');

        $page = Input::get('page', 0);
        $perPage = Input::get('per_page', 30);

        try {
            $query = Booth::where('b_type', '=', 1)->with(['user']);
            if ($key) {
                $query = $query->where(function ($q) use ($key) {
                    $q->where('b_product_source', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_product_category', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_desc', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_title', 'LIKE', '%'.$key.'%');
                });
            }
            if ($school) {
                $query = $query->where('s_id', '=', $school);
            }
            $list = $query->paginate($perPage);
            $data = [];
            foreach ($list as $key => $booth) {
                $data[] = $booth->showDetail();
            }
            $re = Tools::reTrue('获取便利店成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取便利店失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function maker()
    {
        $school = Input::get('school', 0);
        $key = Input::get('key', '');

        $page = Input::get('page', 0);
        $perPage = Input::get('per_page', 30);

        try {
            $query = Booth::where('b_type', '=', 2)->with([
                'user',
                'products' => function ($q) {
                    $q->take(5);
                }
                ]);
            if ($key) {
                $query = $query->where(function ($q) use ($key) {
                    $q->where('b_product_source', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_product_category', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_desc', 'LIKE', '%'.$key.'%')
                    ->orWhere('b_title', 'LIKE', '%'.$key.'%');
                });
            }
            if ($school) {
                $query = $query->where('s_id', '=', $school);
            }
            $list = $query->paginate($perPage);
            $data = [];
            foreach ($list as $key => $booth) {
                $detail = $booth->showDetail();
                $products = [];
                if (!empty($booth->products)) {
                    $tmp = [];
                    foreach ($booth->products as $key => $product) {
                        $tmp['id'] = $product->p_id;
                        $imgArr = explode(',', $product->p_imgs);
                        $img = '';
                        if (!empty($imgArr)) {
                            $img = array_pop($imgArr);
                        }
                        $tmp['img'] = $img;
                        $products[] = $tmp;
                    }
                }
                $detail['prodcts'] = $products;
                $data[] = $detail;
            }
            $re = Tools::reTrue('获取创的店成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取创的店失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listBooth()
    {
        $u_id = Input::get('owner', 0);

        try {
            $data = Booth::with(['user'])->where('u_id', '=', $u_id)->where('b_status', '=', 1)->get();
            $list = [];
            foreach ($data as $key => $booth) {
                $tmp = $booth->showDetail();
                $products_count = Product::where('b_id', '=', $booth->b_id)->where('p_status', '=', 1)->count();
                $tmp['prodct_count'] = $products_count;
                $list[] = $tmp;
            }
            $re = Tools::reTrue('获取我的所有店铺成功', $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取他的所有店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getBooth($id)
    {
        try {
            $booth = Booth::find($id);
            if (empty($booth->b_id)) {
                throw new Exception("无法获取到请求的店铺", 7001);
            }
            if ($booth->b_status != 1) {
                throw new Exception("店铺当前不可用", 7001);
            }
            $booth->load('user');
            $boothInfo = $booth->showDetail();
            $products_count = Product::where('b_id', '=', $booth->b_id)->where('p_status', '=', 1)->count();
            $boothInfo['prodct_count'] = (int)$products_count;
            $data = ['booth' => $boothInfo];
            $re = Tools::reTrue('获取他的店铺成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取他的店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listProduct()
    {
        $b_id = Input::get('booth', 0);
        $key = Input::get('key', '');

        $page = Input::get('page', 0);
        $perPage = Input::get('per_page', 30);

        try {
            if (!$b_id) {
                throw new Exception("请传入店铺ID", 7001);
            }
            $query = Product::with(['promo', 'quantity'])->where('b_id', '=', $b_id);
            if ($key) {
                $query = $query->where(function ($q) use ($key) {
                    $q->where('p_title', 'LIKE', '%'.$key.'%')
                    ->orWhere('p_desc', 'LIKE', '%'.$key.'%');
                });
            }
            $list = $query->paginate($perPage);
            $data = [];
            foreach ($list as $key => $product) {
                $data[] = $product->showInList();
            }
            $re = Tools::reTrue('获取产品列表成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取产品列表失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getProduct($id)
    {
        try {
            $product = Product::with(['quantity',
                'promo',
                'replies' => function ($q) {
                    $q->with(['user', 'children' => function ($qq) {
                        $qq->where('r_status', '=', 1)->orderBy('created_at', 'DESC');
                    }])->where('to_r_id', '=', 0)->where('r_status', '=', 1)->where('to_u_id', '=', 0)->orderBy('created_at', 'DESC');
                }])->find($id);
            if (!empty($product->promo)) {
                $product->promo->load('praises');
            }
            if (empty($product->p_id)) {
                throw new Exception("无法找到请求的产品", 7001);
            }
            $data = $product->showDetail();
            $re = Tools::reTrue('获取产品成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取产品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postPromoPraise($id)
    {
        $token = Input::get('token');
        $u_id = Input::get('u_id');
        $type = Input::get('type');
        $promo = PromotionInfo::find($id);
        try {
            if (empty($promo->p_id)) {
                throw new Exception("请求的促销信息不存在", 7001);
            }
            $user = User::chkUserByToken($token, $u_id);
            $result = 2000;
            if ($type == 1) {
                $promo->p_praise_count += 1;
                $praise = new PromotionPraise();
                $praise->prom_id = $id;
                $praise->u_id = $user->u_id;
                $praise->u_name = $user->u_nickname;
                $praise->addPraise();
                $info = '点赞成功';
            } elseif ($type == 2) {
                if ($promo->p_praise_count > 0) {
                    $promo->p_praise_count -= 1;
                }
                $praise = PromotionPraise::where('prom_id', '=', $id)->where('u_id', '=', $user->u_id)->first();
                if (!empty($praise->t_id)) {
                    $praise->delete();
                }
                $info = '取消赞成功';
            } else {
                throw new Exception("请求的操作不存在", 7001);
            }
            $promo->save();
            $re = Tools::reTrue($info);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '点赞失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postProductReply($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $content = Input::get('content', '');
        $to = Input::get('to', 0);
        $parent = Input::get('parent', 0);

        try {
            $product = Product::find($id);
            $product->p_reply_count += 1;

            $user = User::chkUserByToken($token, $u_id);
            $reply = new ProductReply();
            $reply->p_id = $id;
            $reply->r_content = $content;
            $reply->u_id = $u_id;
            $reply->u_name = $user->u_nickname;

            if ($to > 0) {
                $toUser = User::find($to);
                if (empty($toUser->u_id)) {
                    throw new Exception("回复用户不存在", 7001);
                }
                $reply->to_u_name = $toUser->u_nickname;
            }
            $reply->to_u_id = $to;

            if ($parent > 0) {
                $parentReply = ProductReply::find($parent);
                if (empty($parentReply->r_id)) {
                    throw new Exception("回复对象不存在", 7001);
                }
            }
            $reply->to_r_id = $parent;
            $reply->addReply();
            $product->save();
            $re = Tools::reTrue('回复成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '恢复失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postBoothFollow($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $type = Input::get('type', 1);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $follow = new BoothFollow();
            $follow->b_id = $id;
            $follow->u_id = $u_id;
            if ($type == 1) {
                $msg = '关注成功';
                $follow->follow();
            } elseif ($type == 2) {
                $msg = '取消关注成功';
                $follow->unfollow();
            }
            $re = Tools::reTrue($msg);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '关注操作失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listCarts()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $list = Cart::with(['product', 'booth'])->where('u_id', '=', $u_id)->whereIn('c_status', [0,1])->get();
            $data = [];
            foreach ($list as $key => $cart) {
                $data[] = $cart->showInList();
            }
            $re = Tools::reTrue('获取购物车列表成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取购物车列表失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postCart()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $p_id = Input::get('product');
        $b_id = Input::get('booth');
        $quantity = Input::get('quantity');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $cart = Cart::where('p_id', '=', $p_id)->where('u_id', '=', $u_id)->where('c_status', '=', 1)->first();
            // add new when there is not any cart before
            if (empty($cart->c_id)) {
                $cart = new Cart();
                $cart->b_id = $b_id;
                $cart->u_id = $u_id;
                $cart->p_id = $p_id;
                $cart->c_quantity = $quantity;
                $cart->addCart();
            // cumilate quantity
            } else {
                $quantityOri = $cart->c_quantity;
                $cart->c_quantity += $quantity;
                $cart->updateCart($quantityOri);
            }

            $re = Tools::reTrue('添加购物车成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), $e->getMessage());
        }
        return Response::json($re);
    }

    public function putCart($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $quantity = Input::get('quantity');

        try {
            if ($quantity == 0) {
                throw new Exception("请传入有效的库存", 7001);
            }
            $user = User::chkUserByToken($token, $u_id);
            $cart = Cart::find($id);
            if (empty($cart->c_id)) {
                throw new Exception("无法获取到购物车", 7001);
            }
            if ($cart->c_status != 1 || $cart->u_id != $u_id) {
                throw new Exception("该购物车已经失效", 7001);
            }
            $quantityOri = $cart->c_quantity;
            $cart->c_quantity = $quantity;
            $cart->updateCart($quantityOri);
            $re = Tools::reTrue('添加购物车成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '添加购物车失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function delCart($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $cart = Cart::find($id);
            if (empty($cart->c_id)) {
                throw new Exception("无法获取到购物车", 7001);
            }
            if ($cart->c_status != 1 || $cart->u_id != $u_id) {
                throw new Exception("该购物车已经失效", 7001);
            }
            $cart->removeCart();
            $re = Tools::reTrue('删除购物车成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '删除购物车失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postOrder()
    {
        $now = new DateTime();
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $amount_origin = Input::get('amount_origin', 0);
        $amount = Input::get('amount', 0);
        $shipping_fee = Input::get('shipping_fee', 0);
        $shipping_name = Input::get('shipping_name', '');
        $shipping_phone = Input::get('shipping_phone', '');
        $shipping_address = Input::get('shipping_address', '');
        $shipping = Input::get('shipping', 1);
        $delivery_time = Input::get('delivery_time', $now->format('Y-m-d H:i:s'));
        $remark = Input::get('remark', '');

        $carts = Input::get('carts', '');

        try {
            $carts = explode(',', $carts);
            if (!is_array($carts) || empty($carts)) {
                throw new Exception("请传入有效的购物车", 1);
            }
            $user = User::chkUserByToken($token, $u_id);
            $list = Cart::whereIn('c_id', $carts)->get();
            $groups = [];
            $total_amount = 0;
            $total_amount_origin = 0;
            foreach ($list as $key => $cart) {
                if (empty($groups[$cart->b_id])) {
                    $groups[$cart->b_id]['carts'] = [];
                    $groups[$cart->b_id]['amount_origin'] = 0;
                    $groups[$cart->b_id]['amount'] = 0;
                    $groups[$cart->b_id]['carts_ids'] = '';
                }
                $cart->updateCart($cart->c_quantity);
                $groups[$cart->b_id]['carts'][] = $cart;
                $groups[$cart->b_id]['amount_origin'] += $cart->c_amount_origin;
                $groups[$cart->b_id]['amount'] += $cart->c_amount;
                $groups[$cart->b_id]['carts_ids'][] = $cart->c_id;
                $total_amount += $cart->c_amount;
                $total_amount_origin += $cart->c_amount_origin;
            }

            if (($total_amount_origin != $amount_origin) || ($total_amount != $amount)) {
                throw new Exception("支付金额已刷新, 请重新提交订单", 9001);
            }
            $order_no = Order::generateOrderNo($u_id);
            $order_ids = [];
            foreach ($groups as $key => $group) {
                $order = new Order();
                $order->u_id = $u_id;
                $order->o_amount_origin = $group['amount_origin'];
                $order->o_amount = $group['amount'];
                $order->o_shipping_fee = $shipping_fee;
                $order->o_shipping_name = $shipping_name;
                $order->o_shipping_phone = $shipping_phone;
                $order->o_shipping_address = $shipping_address;
                $order->o_delivery_time = $delivery_time;
                $order->o_shipping = $shipping;
                $order->o_remark = $remark;
                $order->o_number = $order_no;
                $o_id = $order->addOrder();
                $order_ids[$o_id] = $group['carts_ids'];
            }
            Cart::bindOrder($order_ids);
            $re = Tools::reTrue('提交订单成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), $e->getMessage());
        }
        return Response::json($re);
    }

    public function listBoothFollow($id)
    {
        $u_id = Input::get('u_id', 0);
        $school = Input::get('school', 0);
        $profession = Input::get('prof', 0);
        $entry_year = Input::get('entry_year', 0);
        $gender = Input::get('gender', 0);

        $per_page = Input::get('per_page', 30);

        try {
            $user = User::find($u_id);
            $user_contact = UsersContactPeople::find($u_id);
            $query = BoothFollow::with(['follower', 'follower.school'])->where('b_id', '=', $id)->select('booth_follows.*')
            ->leftJoin('users', function ($q) {
                $q->on('users.u_id', '=', 'booth_follows.u_id');
            })->leftJoin('users_contact_peoples', function ($q) {
                $q->on('users_contact_peoples.u_id', '=', 'booth_follows.u_id');
            });

            if ($school) {
                $query = $query->where('users.u_school_id', '=', $user->u_school_id);
            }
            if (!empty($user_contact)) {
                if ($profession) {
                    $query = $query->where('users_contact_peoples.u_prof', '=', $user_contact->u_prof);
                }
                if ($entry_year) {
                    $query = $query->where('users_contact_peoples.u_entry_year', '=', $user_contact->u_entry_year);
                }
            }
            if ($gender) {
                $query = $query->where('users.u_sex', '=', $gender);
            }
            $list = $query->paginate($per_page);
            $data = [];
            foreach ($list as $key => $follow) {
                $data[] = $follow->follower->showInList();
            }
            $re = Tools::reTrue('获取粉丝成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取粉丝失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
