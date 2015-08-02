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
                'booth',
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
            $data = Booth::where('u_id', '=', $u_id)->where('b_status', '=', 1)->get();
            $list = [];
            foreach ($data as $key => $booth) {
                $tmp = $booth->showDetail();
                $products_count = Product::where('b_id', '=', $booth->b_id)->where('p_status', '=', 1)->count();
                $tmp['prodct_count'] = $products_count;
                $list[] = $tmp;
            }
            $re = Tools::reTrue('获取我的所有店铺成功', $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取我的所有店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getBooth($id)
    {
        try {
            $booth = Booth::find($id);
            if (empty($booth->b_id) || $booth->u_id != $u_id) {
                throw new Exception("无法获取到请求的店铺", 7001);
            }
            if ($booth->b_status != 1) {
                throw new Exception("店铺当前不可用", 7001);                
            }
            $boothInfo = $booth->showDetail();
            $data = ['booth' => $boothInfo];
            $re = Tools::reTrue('获取我的店铺成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取我的店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listProduct()
    {
        $b_id = Input::get('booth', 0);
        $key = Input::get('key', '');
        $site = Input::get('site', 0);
        $school = Input::get('school', 0);
        
    }

    public function getProduct()
    {

    }
}
