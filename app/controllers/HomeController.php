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
        $img_token = Input::get('img_token', '');
        try {
            $funding = CrowdFunding::with(['eventItem'])->find(41);
            if ($img_token) {
                $img = new Img('event', $img_token);
                $cover_img = $img->replace(41, 'cover_img');
                var_dump($cover_img);
            }

        } catch (Exception $e) {
            var_dump($e);
        }
    }
}
