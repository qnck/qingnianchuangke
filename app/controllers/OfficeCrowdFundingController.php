<?php
/**
*
*/
class OfficeCrowdFundingController extends \BaseController
{
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
                $funding->active_at = Tools::getNow();
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
