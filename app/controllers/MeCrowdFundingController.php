<?php
/**
*
*/
class MeCrowdFundingController extends \BaseController
{
    public function postCrowFunding()
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

        $price = Input::get('price', 0);
        $quantity = Input::get('quantity', 0);
        $is_limit = Input::get('is_limit', 0);

        $img_token = Input::get('img_token', '');

        $content = urldecode($content);

        DB::beginTransaction();
        try {
            $user = User::chkUserByToken($token, $u_id);
            $user->load('booth', 'profileBase', 'school');
            if (empty($user->profileBase)) {
                throw new Exception("请先提交个人资料审核", 3004);
            }
            if ($user->profileBase->u_status != 1) {
                throw new Exception("您的个人资料还未通过审核", 3004);
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

            // add funding
            $crowd_funding = new CrowdFunding();
            $crowd_funding->u_id = $u_id;
            $crowd_funding->b_id = $booth->b_id;
            $crowd_funding->s_id = $user->school->t_id;
            $crowd_funding->c_id = $user->school->t_city;
            $crowd_funding->c_title = $title;
            $crowd_funding->c_brief = $brief;
            $crowd_funding->c_yield_desc = $yield_desc;
            $crowd_funding->c_content = $content;
            $crowd_funding->c_yield_time = $yield_time;
            $crowd_funding->c_time = $time;
            $crowd_funding->c_shipping = $shipping;
            $crowd_funding->c_shipping_fee = $shipping_fee;
            $crowd_funding->c_target_amount = $amount;
            $crowd_funding->c_cate = $cate;
            $crowd_funding->c_open_file = $open_file;
            $crowd_funding->addCrowdFunding();

            if ($img_token) {
                $imgObj = new Img('crowd_funding', $img_token);
                $crowd_funding->c_imgs = $imgObj->getSavedImg($crowd_funding->c_id);
                $crowd_funding->save();
            }

            // add funding product
            $funding_product = new CrowdFundingProduct();
            $funding_product->cf_id = $crowd_funding->c_id;
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
}
