<?php
/**
*
*/
class MarketController extends \BaseController
{
    public function index()
    {
        $site = Input::get('s_id', 0);
        $school = Input::get('school', 0);
        $key = Input::get('key', '');

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
            $query = $query->select('promotion_infos.*');
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
            $pagination = ['per_page' => $list->getPerPage(), 'page' => $list->getCurrentPage(), 'total_record' => $list->getTotal(), 'total_page' => $list->getLastPage()];
            $data = [];
            foreach ($list as $key => $product) {
                $data[] = $product->showInListWithProduct();
            }
            $re = Tools::reTrue('获取首页商品成功', $data, $pagination);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取首页商品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function convenient()
    {

    }

    public function maker()
    {
        
    }
}
