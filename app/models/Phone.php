<?php

/**
* 
*/
class Phone extends Eloquent{

	private $_mobile = '';

	private function baseValidate(){
		$validator = Validator::make(
			['mobile' => $this->_mobile],
			['mobile' => 'required|digits:11']
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);
		}else{
			return true;
		}
	}
	
	function __construct($mobile){
		$this->_mobile = $mobile;
		$this->baseValidate();
	}

	public function sendText($content, $sendLevel = TxtMessage::SEND_NORMAL){
		$now = new DateTime();
		$txt = new TxtMessage();
		$txt->t_send_level = $sendLevel;
		$txt->t_mobile = $this->_mobile;
		$txt->t_content = $content;
		$txt->send_at = $now->format('Y-m-d H:i:s');
		return $txt->sendMessage();
	}

	public function sendVCode(){
		$code = new VerificationCode();
		$code->generateCode();
		$expire = new DateTime();
		$expire->modify('+5mins');
		$code->expire_at = $expire->format('Y-m-d H:i:s');
		$code->v_reuse = 0;
		$code->verifiable_id = $this->_mobile;
		$code->verifiable_type = 'Phone';
		$code->save();
		$txt = '您的验证码是 '.$code->v_code;
		$this->sendText($txt, TxtMessage::SEND_FAST);
		return $code->v_code;
	}

	public function authVCode($code){
		$now = new DateTime();
		$now = $now->format('Y-m-d H:i:s');
		$vCode = VerificationCode::where('v_code', '=', $code)
		->where('verifiable_id', '=', $this->_mobile)
		->where('verifiable_type', '=', 'Phone')
		->where('v_reuse', '=', 0)
		->where('expire_at', '>', $now)
		->whereNull('verify_at')
		->first();
		if(!isset($vCode->v_id)){
			throw new Exception("the code is invalide", 1);			
		}else{
			$vCode->verify_at = $now;
			$vCode->save();
			return true;
		}
	}

	public function verification_code(){
		$this->morphOne('VerificationCode', 'verifiable');
	}
}