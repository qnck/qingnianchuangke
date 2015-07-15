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
            $list = $query->paginate(30);
            $data = [];
            foreach ($list as $key => $school) {
                $data[] = $school->showInList();
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取学校成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取学校失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }

    public function getCities()
    {
        $key = Input::get('key', '');
        $province = Input::get('province', '');
        
        try {
            $query = DicCity::where('c_id', '>', 0);
            if ($key) {
                $query = $query->where('c_name', 'LIKE', '%'.$key.'%');
            }
            if ($province) {
                $query = $query->where('c_province_id', '=', $province);
            }
            $list = $query->paginate(30);
            $data = [];
            foreach ($list as $key => $city) {
                $data[] = $city->showInList();
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取城市成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取城市失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }
}
