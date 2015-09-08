<?php
/**
*
*/
class OfficeFundController extends \BaseController
{

    public function listRepayments($id)
    {
        try {
            $list = Repayment::where('f_id', '=', $id)->get();
            $data = [];
            foreach ($list as $key => $repay) {
                $data[] = $repay->showInList();
            }
            $re = Tools::reTrue('获取提款计划成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取提款计划失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
    
    public function allocateRepayment($id)
    {
        try {
            $repay = Repayment::find($id);
            if (empty($repay)) {
                throw new Exception("没有找到放款明细", 10001);
            }
            $repay->allocate();
            $re = Tools::reTrue('放款成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '放款失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
