<?php
/**
*
*/
class DicController extends \BaseController
{
    public function getSchools()
    {
        $key = Input::get('key', '');
        $province = Input::get('province', '');
        $city = Input::get('city', '');

        try {
            $query = DicSchool::where('t_type', '=', '1');
            if ($key) {
                $query = $query->where('t_name', 'LIKE', '%'.$key.'%');
            }
            if ($province) {
                $query = $query->where('t_province', '=', $province);
            }
            if ($city) {
                $query = $query->where('t_city', '=', $city);
            }
            $list = $query->get();
            $data = [];
            foreach ($list as $key => $school) {
                $data[] = $school->showInList();
            }
            $paginate = ['total_record' => $list->count(), 'total_page' => 1, 'per_page' => $list->count(), 'current_page' => 1];

            $re = ['result' => 2000, 'data' => $data, 'info' => '获取学校成功', 'pagination' => $paginate];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取学校失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }

    public function getCities()
    {
        $key = Input::get('key', '');
        $province = Input::get('province', '');
        $ver = Input::get('ver', 0);

        $currentVer = DicCity::VER;

        if ($ver >= $currentVer) {
            return Response::json(['result' => 2000, 'data' => [], 'info' => '获取城市成功', 'ver' => $currentVer]);
        }
        
        try {
            $query = DicCity::where('c_id', '>', 0);
            if ($key) {
                $query = $query->where('c_name', 'LIKE', '%'.$key.'%');
            }
            if ($province) {
                $query = $query->where('c_province_id', '=', $province);
            }
            $list = $query->get();
            $data = [];
            foreach ($list as $key => $city) {
                $data[] = $city->showInList();
            }
            $paginate = ['total_record' => $list->count(), 'total_page' => 1, 'per_page' => $list->count(), 'current_page' => 1];
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取城市成功', 'ver' => $currentVer, 'pagination' => $paginate];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取城市失败:'.$e->getMessage(), 'ver' => $currentVer];
        }

        return Response::json($re);
    }

    public function getBanks()
    {
        $key = Input::get('key', '');
        $ver = Input::get('ver', 0);

        $currentVer = DicBank::VER;

        if ($ver >= $currentVer) {
            return Response::json(['result' => 2000, 'data' => [], 'info' => '获取银行成功', 'ver' => $currentVer]);
        }
        
        try {
            $query = DicBank::where('b_id', '>', 0);
            if ($key) {
                $query = $query->where('b_name', 'LIKE', '%'.$key.'%');
            }
            $list = $query->get();
            $data = [];
            foreach ($list as $key => $city) {
                $data[] = $city->showInList();
            }
            $paginate = ['total_record' => $list->count(), 'total_page' => 1, 'per_page' => $list->count(), 'current_page' => 1];
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取银行成功', 'ver' => $currentVer, 'pagination' => $paginate];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取银行失败:'.$e->getMessage(), 'ver' => $currentVer];
        }

        return Response::json($re);
    }
}
