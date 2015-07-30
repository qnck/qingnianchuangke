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
            $user->latitude = $lat;
            $user->longitude = $lng;
            $user->save();
            $re = Tools::reTrue('更新用户地址信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '更新用户地址信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function updateBooth($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $lat = Input::get('lat', 0);
        $lng = Input::get('lng', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);

            $booth = Booth::find($id);
            if ($booth->u_id != $user->u_id) {
                throw new Exception("您无法操作该店铺", 7001);
            }
            $booth->latitude = $lat;
            $booth->longitude = $lng;
            $booth->save();
            $re = Tools::reTrue('更新店铺地址成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '更新店铺地址:'.$e->getMessage());
        }
    }
}
