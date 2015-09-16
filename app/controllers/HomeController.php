<?php

use \Illuminate\Support\Collection;

class HomeController extends BaseController {
    
    public function v1()
    {
        $timeEnd = strtotime('2015-06-07');
        $now = time();
        $remain = $timeEnd - $now;
        $day = ceil($remain/86400);
        if ($day == 0) {
            $timerStr = 'THAT`S THE DAY';
        } elseif ($day > 0) {
            $timerStr = $day . 'DAYS';
        } else {
            $timerStr = '';
        }
        return View::make('blade.welcome.v1')->with('timer', $timerStr);
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
