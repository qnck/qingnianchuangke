<?php

/**
* 
*/
class VerificationController extends \BaseController{
	
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