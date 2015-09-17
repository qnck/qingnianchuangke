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

    public function interviewFund($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');

        try {
            $fund = Fund::find($id);
            if (empty($fund)) {
                throw new Exception("没有找到请求的基金记录", 10001);
            }
            if ($fund->t_status > 2) {
                throw new Exception("已经审核过了", 10001);
            }
            if ($check) {
                $fund->t_status = 3;
            } else {
                $fund->t_status = 1;
            }
            $fund->remark = $remark;
            $fund->interview();
            $re = Tools::reTrue('操作成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '操作失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
    
    public function allocateRepayment($id)
    {
        $comment = Input::get('comment', '');
        $img_token = Input::get('img_token', '');

        DB::beginTransaction();
        try {
            $repay = Repayment::find($id);
            if (empty($repay)) {
                throw new Exception("没有找到放款明细", 10001);
            }
            $repay->allocate($comment);
            if ($img_token) {
                $imgObj = new Img('loan', $img_token);
                $imgs = $imgObj->getSavedImg($repay->t_id);
                $repay->imgs = $imgs;
                $repay->save();
            }

            $re = Tools::reTrue('放款成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '放款失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }
}
