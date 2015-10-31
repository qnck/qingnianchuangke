<?php
/**
*
*/
class CrowdFundingController extends \BaseController
{

    public function getCate()
    {
        $data = CrowdFunding::getCrowdFundingCate();
        $re = Tools::reTrue('获取分类成功', $data);
        return Response::json($data);
    }

    public function listCrowdFunding()
    {
        $per_page = Input::get('per_page', 30);

        $cate = Input::get('cate', 0);

        $range = Input::get('range', 1);
        $city = Input::get('city', 0);
        $school = Input::get('school', 0);

        try {
            $query = CrowdFunding::with(['city', 'school', 'user', 'product'])->where('c_status', '>', 2);
            if ($cate) {
                $query = $query->where('c_cate', '=', $cate);
            }
            if ($city && $range == 2) {
                $query = $query->where('c_id', '=', $city);
            }
            if ($school && $range = 3) {
                $query = $query->where('s_id', '=', $school);
            }
            $list = $query->paginate($per_page);
            $data = [];
            foreach ($list as $key => $funding) {
                $data[] = $funding->showInList();
            }
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取众筹失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getCrowdFunding($id)
    {
        try {
            $crowdfunding = CrowdFunding::find($id);
            $crowdfunding->load(['replies']);
            $data = $crowdfunding->showDetail();
            $re = Tools::reTrue('获取众筹成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse('获取众筹失败');
        }
        return Response::json($re);
    }

    public function postReply($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $to = Input::get('to', 0);
        $to_u_id = Input::get('to_u_id', 0);
        $content = Input::get('content', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $to_user = User::find($to_u_id);
            if (empty($to_user)) {
                $to_u_id = 0;
                $to_u_name = '';
            } else {
                $to_u_name = $to_user->u_nickname;
            }
            $cf = CrowdFunding::find($id);
            $reply = [
                'to_id' => $to,
                'created_at' => Tools::getNow(),
                'content' => $content,
                'u_id' => $u_id,
                'u_name' => $user->u_nickname,
                'status' => 1,
                'to_u_id' => $to_u_id,
                'to_u_name' => $to_u_name,
            ];
            $replyObj = new Reply($reply);
            $cf->replies()->save($replyObj);
            $re = Tools::reTrue('回复成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '回复失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
