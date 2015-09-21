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
                $booth = Booth::find($fund->b_id);
                if (empty($booth)) {
                    throw new Exception("无与基金相关的店铺数据", 10001);
                }
                $booth->b_status = 2;
                $booth->save();
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

    public function listRepaiedFund()
    {
        $per_page = Input::get('per_page', 30);

        try {
            $query = Fund::with(['booth', 'loans'])->where('t_status', '>', 2);
            $list = $query->paginate($per_page);
            $data = [];
            foreach ($list as $key => $fund) {
                $tmp = $fund->showDetail();
                $tmp['last_income'] = $fund->getCurrentPeriodIncome();
                $data[] = $tmp;
            }
            $re = Tools::reTrue('获取基金收入列表成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取基金收入列表成功:'.$e->getMessage());
        }
        return Response::json($re);
    }
    
}
