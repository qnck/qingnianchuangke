<?php

/**
* every verify go through here
*/
class VerificationController extends \BaseController{
	
	/**
	 * generate and send a verification code
	 * @return response json output
	 */
	public function getVCode(){
		$mobile = Input::get('mobile');
		try {
			$phone = new Phone($mobile);
			if($re = $phone->sendVCode()){
				$re = ['error' => '', 'v_code' => $re];
			}else{
				$re = ['error' => 'unknow error'];
			}
		} catch (Exception $e) {
			$re = ['error' => $e->getMessage()];
		}
		return Response::json($re);
	}


}