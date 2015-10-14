<?php
/**
*
*/
class OfficeBoothController extends \BaseController
{
    public function listBooths()
    {
        $alloc = Input::get('alloc', 0);
        $interview = Input::get('interview', 0);

        $per_page = Input::get('per_page', 10000000);

        try {
            $query = Booth::with(['fund' => function ($q) {
            },
            'fund.loans',
            'user'])->select('booths.*');

            $query = $query->leftJoin('funds', function ($q) {
                $q->on('funds.b_id', '=', 'booths.b_id');
            });

            if ($alloc == 1) {
                $query = $query->where('funds.t_status', '>', 2)->where('booths.b_status', '=', 1);
            }
            if ($interview == 1) {
                $query = $query->where('funds.t_status', '=', 2)->where('booths.b_status', '<>', 1);
            }
            $list = $query->paginate($per_page);
            $data['rows'] = [];
            foreach ($list as $key => $booth) {
                $data['rows'][] = $booth->showInOffice();
            }
            $data['total'] = $list->getTotal();
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
                if ($interview != 1) {
                    $booth->b_status = 1;
                }
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

    public function listLoans($id)
    {
        try {
            $booth = Booth::with(['fund', 'fund.loans'])->find($id);
            if (empty($booth)) {
                throw new Exception("没有找到请求的店铺", 10001);
            }
            $income = $booth->fund->getCurrentPeriodIncome();

            $boothData = $booth->showDetail();
            $fundData = $booth->fund->showDetail();
            $data = ['last_income' => $income, 'booth' => $boothData, 'fund' => $fundData];
            $re = Tools::reTrue('获取店铺贷款信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取店铺信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
