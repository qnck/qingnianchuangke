<?php
/**
*
*/
class WechatController extends \BaseController
{
    public function getHengdaUsers()
    {
        try {
            $wecht = new WechatApi('hengda');
            $re = $wecht->getFollowUser();
            $re = $wecht->getUsers($re['data']['openid']);
            $re = Tools::reTrue('获取恒大微信用户成功', $re);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取恒大微信用户失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
