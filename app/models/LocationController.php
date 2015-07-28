<?php
/**
*
*/
class LocationController extends \BaseController
{
    public function getNearbyUsers()
    {
        $re = ['reset' => 2000, 'data' => [], 'info' => '获取附近的用户成功'];
    }

    public function getNearbyStores()
    {
        $re = ['reset' => 2000, 'data' => [], 'info' => '获取附近的店铺成功'];
    }

    public function getNearbyActivities()
    {
        $re = ['reset' => 2000, 'data' => [], 'info' => '获取附近的活动成功'];
    }

    public function updateUser($id)
    {
        $token = Input::get('token', '');
        $lat = Input::get('lat', 0);
        $lng = Input::get('lng', 0);

        try {
            $user = User::chkUserByToken($token, $id);

            if (!$lat || !$lng) {
                throw new Exception("请传入有效的位置信息", 8001);
            }
            $booths = Booth::where('u_id', '=', $id)->get();
            if (count($booths) > 0) {
                foreach ($booths as $booth) {
                    $booth->latitude = $lat;
                    $booth->longitude = $lng;
                    $booth->save();
                }
            }
            $user->latitude = $lat;
            $user->longitude = $lng;
            $user->save();
            $re = ['result' => 2000, 'data' => [], 'info' => '更新用户地址信息成功'];
        } catch (Exception $e) {
            $re = ['result' => 8001, 'data' => [], 'info' => '更新用户地址信息失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }
}
