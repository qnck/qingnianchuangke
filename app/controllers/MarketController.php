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

        $page = Input::get('page', 0);
        $perPage = Input::get('per_page', 30);

        try {
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
            foreach ($list as $key => $product) {
                $data[] = $product->showInListWithProduct();
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
            $re = Tools::reTrue('获取便利店成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取便利店失败:'.$e->getMessage());
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

    public function postCart()
    {

    }

    public function putCart($id)
    {

    }

    public function delCart($id)
    {

    }
}
