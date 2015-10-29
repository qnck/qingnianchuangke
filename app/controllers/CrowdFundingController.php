<?php
/**
*
*/
class CrowdFundingController extends \BaseController
{
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
}
