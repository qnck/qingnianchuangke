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
        $remark = Input::get('remark', '');
        $interview = Input::get('interview', 0);

        DB::beginTransaction();
        try {
            $booth = Booth::find($id);
            if (empty($booth)) {
                throw new Exception("无法获取到店铺信息", 10001);
            }

            // get fund if there is one
            if ($booth->b_with_fund) {
                $fund = Fund::where('b_id', '=', $id)->first();
                if (empty($fund)) {
                    throw new Exception("基金数据不匹配", 10001);
                }
            } else {
                $fund = false;
            }
            if ($check == 1) {
                if ($booth->b_status == 1) {
                    throw new Exception("店铺已经审核过了", 10002);
                }
                $booth->b_status = 1;
                if ($fund) {
                    $fund->censorPass($interview);
                }
            } elseif ($check == 0) {
                $booth->b_status = 2;
                $booth->remark = $remark;
                if ($fund) {
                    $fund->censorFailed();
                }
            }
            $booth->censor();
            $re = Tools::reTrue('审核店铺成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核店铺失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }
}
