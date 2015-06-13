<?php

/**
* 
*/
class VerificationCode extends Eloquent{

	public $primaryKey = 'v_id';

	const CODE_TYPE_NUMERIC = 1;
	const CODE_TYPE_ALPHA_NUMERIC = 2;

	public function verifiable(){
        return $this->morphTo();
    }

    public function generateCode($type = VerificationCode::CODE_TYPE_NUMERIC){
    	switch ($type) {
    		case VerificationCode::CODE_TYPE_ALPHA_NUMERIC:
    			$code = Str::random(6);
    			break;

    		case VerificationCode::CODE_TYPE_NUMERIC:
    			$code = mt_rand(100, 999999);
    			$code = sprintf('%06d', $code);
    			break;
    		
    		default:
    			throw new Exception("unknow code type", 1);
    			break;
    	}
		$this->v_code = $code;
	}
}