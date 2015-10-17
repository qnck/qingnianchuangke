<?php
/**
*
*/
class AdController extends \BaseController
{
    public function listIndexTop()
    {
        $data = [
            ['url' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/1_inner.jpg', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/1.png', 'title' => '青年创'],
            ['url' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/2_inner.jpg', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/2.png', 'title' => '青年创']
        ];
        $re = Tools::reTrue('获取首页广告成功', $data);
        return Response::json($re);
    }
}
