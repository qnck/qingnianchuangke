<?php
/**
*
*/
class AppController extends \BaseController
{
    public function getConfig()
    {
        $init['app_ver'] = '0.0.1';
        $init['api_ver'] = 'v0';
        $init['force_upgrade'] = 1;
        $init['init_img'] = 'http://img.54qnck.com/img/sys/load.png';
        $init['app_download_link'] = 'http://img.54qnck.com/download/CK.apk';
        $init['desc'] = '新加了个若干功能, 修复了若干bug';

        $data['init'] = $init;
        $data['other'] = null;
        $re = Tools::reTrue('请求成功', $data);
        return Response::json($re);
    }
}
