<?php

/**
* every verify go through here
*/
class VerificationController extends \BaseController
{
    
    /**
     * generate and send a verification code
     * @return response json output
     */
    public function getVCode()
    {
        $mobile = Input::get('mobile');
        try {
            $phone = new Phone($mobile);
            $code = $phone->sendVCode();
            $data = ['vcode' => $code];
            $re = ['data' => $data, 'result' => 2000, 'info' => '验证码已经发送'];
        } catch (Exception $e) {
            $re = ['data' => [], 'result' => 2001, 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }
}
