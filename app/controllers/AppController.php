<?php
/**
*
*/
class AppController extends \BaseController
{
    public function getConfig()
    {
        $data['app_ver'] = '0.0.1';
        $data['api_ver'] = 'v0';
        $data['init_img'] = 'http://www.test.img.com/img.png';
        $data['app_download_link'] = 'http://www.download.com/app.pkg';
        $re = Tools::reTrue('请求成功', $data);
        return Response::json($re);
    }
}
