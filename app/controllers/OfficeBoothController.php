<?php
/**
*
*/
class OfficeBoothController extends \BaseController
{
    public function listBooths()
    {
        $per_page = Input::get('per_page', 30);

        try {
            $query = Booth::with(['fund' => function ($q) {

            },
            'fund.loans',
            'user']);
            $list = $query->paginate($per_page);
            $data = [];
            foreach ($list as $key => $booth) {
                $data[] = $booth->showInAdmin();
            }
            $re = Tools::reTrue('获取店铺列表成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorBooth($id)
    {
        $check = Input::get('check', 0);
        try {
            $booth = Booth::find($id);
            if (empty($booth)) {
                throw new Exception("无法获取到店铺信息", 10001);
            }
            if ($check == 1) {
                if ($booth->b_status == 1) {
                    throw new Exception("店铺已经审核过了", 10001);
                }
                $booth->b_status = 1;
            } elseif ($check == 0) {
                $booth->b_status = 2;
            }
            $booth->save();
            $re = Tools::reTrue('审核店铺成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
