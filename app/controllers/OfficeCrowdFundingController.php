<?php
/**
*
*/
class OfficeCrowdFundingController extends \BaseController
{
    public function postFunding()
    {
        $u_id = Tools::getOfficialUserId();
        $b_id = Tools::getOfficialBoothId();

        $title = Input::get('title', '');
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

        if (empty($active_at)) {
            $active_at = Tools::getNow();
        }

        $mobile = Input::get('mobile', '');

        $price = Input::get('price', 0);
        $quantity = Input::get('quantity', 0);
        $is_limit = Input::get('is_limit', 0);

        $img_token = Input::get('img_token', '');

        $content = urldecode($content);

        DB::beginTransaction();
        try {
            $user = User::find($u_id);
            $user->load('profileBase', 'school');
            $booth = Booth::find($b_id);

            // add funding
            $crowd_funding = new CrowdFunding();
            $crowd_funding->u_id = $u_id;
            $crowd_funding->b_id = $booth->b_id;
            $crowd_funding->s_id = $user->school->t_id;
            $crowd_funding->c_id = $user->school->t_city;
            $crowd_funding->pv_id = $user->school->t_province;
            $crowd_funding->c_title = $title;
            $crowd_funding->c_brief = $brief;
            $crowd_funding->c_yield_desc = $yield_desc;
            $crowd_funding->c_content = $content;
            $crowd_funding->c_yield_time = $yield_time;
            $crowd_funding->u_mobile = $mobile;
            $crowd_funding->c_time = $time;
            $crowd_funding->c_shipping = $shipping;
            $crowd_funding->c_shipping_fee = $shipping_fee;
            $crowd_funding->c_target_amount = $amount;
            $crowd_funding->c_local_only = $local_only;
            $crowd_funding->c_open_file = $open_file;
            $date_obj = new DateTime($active_at);
            $date_obj->modify('+'.$time.' days');
            $crowd_funding->end_at = $date_obj->format('Y-m-d H:i:s');
            $crowd_funding->c_status = 4;
            $crowd_funding->c_cate = 8;

            $crowd_funding->addCrowdFunding();

            if ($img_token) {
                $imgObj = new Img('crowd_funding', $img_token);
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

    public function listFunding()
    {
        $per_page = Input::get('per_page', 30);

        try {
            $list = CrowdFunding::with('product')->paginate($per_page);
            $data['rows'] = [];
            foreach ($list as $key => $funding) {
                $data['rows'][] = $funding->showInList();
            }
            $data['total'] = $list->getTotal();
            $re = Tools::reTrue('获取众筹列表成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse('获取众筹列表失败');
        }
        return Response::json($re);
    }

    public function getFunding($id)
    {
        try {
            $funding = CrowdFunding::find($id);
            if (empty($funding)) {
                throw new Exception("没有找到请求的数据", 10001);
            }
            $data = $funding->showDetail();
            $re = Tools::reTrue('获取众筹信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取众筹信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorFunding($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');
        try {
            $funding = CrowdFunding::find($id);
            if ($check == 1) {
                $funding->c_status = 4;
            } else {
                $funding->c_status = 2;
            }
            $funding->c_remark = $remark;
            $funding->censor();
            $re = Tools::reTrue('审核众筹成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核众筹信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
