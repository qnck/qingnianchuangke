<?php

use \Illuminate\Support\Collection;

class HomeController extends BaseController {
    
    public function v1()
    {
        
    }

    public function index()
    {
        $p = null;
        echo '<a href="qnckinterface.sinaapp.com">http://qnckinterface.sinaapp.com/</a>';
    }

    public function about()
    {
        echo "ABOUT US";
    }
}
