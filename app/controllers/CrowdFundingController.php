<?php
/**
*
*/
class CrowdFundingController extends \BaseController
{
    public function listCrowFunding()
    {
        $data = [];
        $tmp = [
            'id' => 1,
            'title' => '众筹众筹众筹',
            'price' => '20.00',
            'amount' => '2324.00',
            'target_amount' => '5000.00',
            'days_left' => '16',
            'school' => [
                'id' => 1,
                'school_name' => '北京大学',
            ],
            'city' => [
                'id' => 1,
                'name' => '北京',
            ],
            'user' => [
                'id' => 20,
                'name' => '学姐',
                'nickname' => '还是学姐',
                'head_img' => "http://qnckimgtest.oss-cn-hangzhou.aliyuncs.com/user/46/head_img.jpg",
            ],
        ];
        $data[] = $tmp;
        $re = Tools::reTrue('获取众筹列表成功', $data);
        return Response::json($re);
    }
}
