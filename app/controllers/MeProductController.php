<?php
/**
*
*/
class MeProductController extends \BaseController
{
    public function postFlea()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        
        $prodName = Input::get('prod_name', '');
        $prodDesc = Input::get('prod_desc', '');
        $prodBrief = Input::get('prod_brief', '');
        $publish = Input::get('publish', 1);
        $product_cate = Input::get('cate');
        if (!$product_cate) {
            $product_cate = 6;
        }

        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $booth = Booth::where('u_id', '=', $u_id)->first();
            if (empty($booth)) {
                $user->load('school');
                $school = $user->school;
                $booth = new Booth();
                $booth->u_id = $u_id;
                $booth->b_type = 7;
                $booth->c_id = $school->t_city;
                $booth->s_id = $school->t_id;
                $booth->b_with_fund = 0;
                $booth->latitude = $user->latitude;
                $booth->longitude = $user->longitude;
                $booth->save();
            }

            $product = new Product();
            $product->b_id = $booth->b_id;
            $product->p_title = $prodName;
            $product->u_id = $u_id;
            $product->p_cost = 0;
            $product->p_price_origin = 0;
            $product->p_price = 0;
            $product->p_discount = 0;
            $product->p_desc = $prodDesc;
            $product->p_brief = $prodBrief;
            $product->p_status = $publish == 1 ? 1 : 2;
            $product->p_cate = $product_cate;
            $product->p_type = 2;
            $p_id = $product->addProduct();
            $quantity = new ProductQuantity();
            $quantity->p_id = $p_id;
            $quantity->b_id = $booth->b_id;
            $quantity->u_id = $u_id;
            $quantity->q_total = 1;

            $quantity->addQuantity();

            if ($imgToken) {
                $imgObj = new Img('product', $imgToken);
                $imgs = $imgObj->getSavedImg($p_id, '', true);
                $product->p_imgs = implode(',', $imgs);
                $product->save();
            }

            $re = Tools::reTrue('添加产品成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '添加产品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function putFlea($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $prodName = Input::get('prod_name', '');
        $prodBrief = Input::get('prod_brief', '');
        $prodDesc = Input::get('prod_desc', '');
        $publish = Input::get('publish', 1);
        $product_cate = Input::get('cate', 7);

        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $product = Product::find($id);

            if (!isset($product->p_id) || $product->u_id != $u_id) {
                throw new Exception("没有找到请求的产品", 1);
            }

            $product->p_title = $prodName;
            $product->p_desc = $prodDesc;
            $product->sort = 1;
            $product->p_cate = $product_cate;
            $product->p_brief = $prodBrief;
            $product->p_status = $publish == 1 ? 1 : 2;

            if ($imgToken) {
                $imgObj = new Img('product', $imgToken);
                $imgs = $imgObj->getSavedImg($id, $product->p_imgs, true);
                $product->p_imgs = implode(',', $imgs);
                $product->save();
            }

            $re = Tools::reTrue('更新产品成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '更新产品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getFlea($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            $data = $product->showDetail();
            $re = Tools::reTrue('获取商品成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取商品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
