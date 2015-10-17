<?php
/**
*
*/
class AdController extends \BaseController
{
    public function listIndexTop()
    {
        $base = Config::get('app.url');
        $data = [
            ['url' => $base.'banner/1', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/1.png', 'title' => '青年创'],
            ['url' => 'http://img.54qnck.com/download/2_inner.jpg', 'img' => 'http://qnck001.oss-cn-hangzhou.aliyuncs.com/banner/2.png', 'title' => '青年创']
        ];
        $re = Tools::reTrue('获取首页广告成功', $data);
        return Response::json($re);
    }
}
