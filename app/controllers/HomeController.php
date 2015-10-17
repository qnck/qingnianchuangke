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
        
        return View::make('blade.index.banner1');
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
