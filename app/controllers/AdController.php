<?php
/**
*
*/
class AdController extends \BaseController
{
    public function listIndexTop()
    {
        $data = [
            ['url' => 'www.54qnck.com', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/1.jpg', 'title' => '青年创'],
            ['url' => 'www.54qnck.com', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/2.jpg', 'title' => '青年创'],
            ['url' => 'www.54qnck.com', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/3.jpg', 'title' => '青年创']
        ];
        $re = Tools::reTrue('获取首页广告成功', $data);
        return Response::json($re);
    }
}
