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
}
