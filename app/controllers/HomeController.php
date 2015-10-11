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

    public function about()
    {
        echo "ABOUT US";
    }
}
