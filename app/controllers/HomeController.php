<?php

use \Illuminate\Support\Collection;

class HomeController extends BaseController {
    
    public function v1()
    {
    }

    public function index()
    {
        $phone = new Phone(18508237273);
        var_dump($phone->sendText('您的验证码是559462，该验证码5分钟内有效。校园创业、购物、交友、找工作就上'));
        exit;
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
}
