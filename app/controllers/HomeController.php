<?php

use \Illuminate\Support\Collection;

class HomeController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |   Route::get('/', 'HomeController@showWelcome');
    |
    */
    public function v1(){
        $timeEnd = strtotime('2015-06-07');
        $now = time();
        $remain = $timeEnd - $now;
        $day = ceil($remain/86400);
        if($day == 0){
            $timerStr = 'THAT`S THE DAY';
        }elseif($day > 0){
            $timerStr = $day . 'DAYS';
        }else{
            $timerStr = '';
        }
        return View::make('blade.welcome.v1')->with('timer', $timerStr);
    }

    public function index(){
        // $msg = new TxtMessage();
        // try {
        //     var_dump($msg->sendMessage('18628320065', '您的验证码999111'));
        // } catch (Exception $e) {
        //     var_dump($e->getMessage());
        // }
        // 
            $time = new DateTime();
            var_dump($time->format('Y-m-d H:i:s'));
            $time->modify('+5mins');
            var_dump($time->format('Y-m-d H:i:s'));

    }
}
