<?php
/**
*
*/
class OfficeDrawContoller extends \BaseController
{
    public function listDraw()
    {
        $status = Input::get('status', null);
        $per_page = Input::get('per_page', 10000000);

        try {
            $query = UsersDraw::with(['bank']);
            if (!empty($status)) {
                $query = $query->where('d_status', '=', (int)$status);
            }
            $list = $query->paginate($per_page);
            $data['rows'] = [];
            foreach ($list as $key => $draw) {
                $data['rows'][] = $draw->showInList();
            }
            $data['total'] = $list->getTotal();
            $re = Tools::reTrue('获取提现列表成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取提现列表失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getDraw($id)
    {
        try {
            $draw = UsersDraw::find($id);
            if (empty($draw)) {
                throw new Exception("请求的数据不存在", 10001);
            }
            $data = $draw->showInList();
            $re = Tools::reTrue('获取提现详细成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取提现详细失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function confirmDraw($id)
    {
        $confirm = Input::get('confirm', 0);
        $comment = Input::get('comment', '');

        $img_token = Input::get('img_token', '');

        DB::beginTransaction();

        try {
            $draw = UsersDraw::find($id);
            if (empty($draw)) {
                throw new Exception("请求的数据不存在", 10001);
            }
            $balance = UsersWalletBalances::find($draw->u_id);
            if (empty($balance)) {
                $balance = new UsersWalletBalances();
                $balance->u_id = $draw->u_id;
            }

            if ($confirm == 1) {
                $balance->deFreez($draw->d_amount);
                $draw->d_status = 1;
                $balance->getOut($draw->d_amount);
            } elseif ($confirm == 0) {
                $draw->d_status = 2;
            } else {
                throw new Exception("只有确认提现/不确认提现", 10001);
            }
            $draw->confirm($comment);
            if ($img_token) {
                $imgObj = new Img('draw', $img_token);
                $imgs = $imgObj->getSavedImg($draw->d_id);
                $draw->imgs = $imgs;
                $draw->save();
            }
            $re = Tools::reTrue('确认提现成功');
            DB::commit();
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '确认提现失败:'.$e->getMessage());
            DB::rollback();
        }
        return Response::json($re);
    }
}
