<?php

use \Illuminate\Support\Collection;

class HomeController extends BaseController {
    
    public function v1()
    {
    }

    public function index()
    {
        return View::make('blade.index.index');
    }

    public function banner1()
    {
        $type = Input::get('share_type', '');
        $download_link = 'http://'.Config::get('app.subdomain.api').'/app/download';
        return View::make('blade.index.banner1')->with('link', $download_link)->with('share_type', $type);
    }

    public function banner2()
    {
        return View::make('blade.index.banner2');
    }

    public function about()
    {
        echo "ABOUT US";
    }

    public function test()
    {
        $oss = new AliyunOss('event', '', 41);
        $re = $oss->exsits('event/41/er_img.873041.jpg');
    }
}
