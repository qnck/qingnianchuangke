<?php
/**
*
*/
class AppController extends \BaseController
{
    public function getConfig()
    {
        $init['app_ver'] = '0.07';
        $init['api_ver'] = 'v0';
        $init['force_upgrade'] = 1;
        $init['init_img'] = 'http://img.54qnck.com/img/sys/load.png';
        $init['app_download_link'] = 'http://img.54qnck.com/download/CK_007.apk';
        $init['desc'] = '新加了个若干功能, 修复了若干bug';

        $data['init'] = $init;
        $data['other'] = null;
        $re = Tools::reTrue('请求成功', $data);
        return Response::json($re);
    }

    public function postFeedback()
    {
        $u_id = Input::get('u_id', 0);
        $comment = Input::get('comment', '');
        $app_ver = Input::get('app_ver', '');

        try {
            $feedback = new AppFeedback();
            $feedback->u_id = $u_id;
            $feedback->comment = $comment;
            $feedback->app_ver = $app_ver;
            $feedback->addFeedback();
            $re = Tools::reTrue('提交成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '提交失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
