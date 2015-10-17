<?php
/**
*
*/
class OfficeProductController extends \BaseController
{
    public function listProduct()
    {
        $per_page = Input::get('per_page', 30);

        try {
            $query = Product::with(['booth', 'quantity', 'user']);
            $list = $query->paginate($per_page);
            $data['rows'] = [];
            foreach ($list as $key => $product) {
                $data['rows'][] = $product->showInList();
            }
            $data['total'] = $list->getTotal();
            $re = Tools::reTrue('获取产品列表成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取产品列表失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getProduct($id)
    {
        try {
            $product = Product::find($id);
            if (empty($product)) {
                throw new Exception("没有找到可用的产品", 10001);
            }

            $product->load(['booth', 'quantity', 'user']);
            $data = $product->showDetail();
            $re = Tools::reTrue('获取产品信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取产品信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function enable($id)
    {
        $status = Input::get('status', 0);
        $remark = Input::get('remark', '');

        try {
            $product = Product::find($id);
            if (empty($product)) {
                throw new Exception("没有找到可用的产品", 10001);
            }
            if ($status == 1) {
                $product->p_status = 1;
            } else {
                $product->p_status = -1;
            }
            $product->p_remark = $remark;
            $product->save();
            $re = Tools::reTrue('修改产品状态成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '修改产品状态失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
