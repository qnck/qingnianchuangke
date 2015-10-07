<?php
/**
*
*/
class AdController extends \BaseController
{
    public function listIndexTop()
    {
        $data = [
            ['url' => 'www.baidu.com', 'img' => 'http://www.baidu.com/img/bdlogo.png', 'title' => 'title1'],
            ['url' => 'www.baidu.com', 'img' => 'http://www.baidu.com/img/bdlogo.png', 'title' => 'title2'],
            ['url' => 'www.baidu.com', 'img' => 'http://www.baidu.com/img/bdlogo.png', 'title' => 'title3']
        ];
        $re = Tools::reTrue('获取首页广告成功', $data);
        return Response::json($re);
    }
}
