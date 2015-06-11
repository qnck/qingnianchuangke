<?php

class User extends Eloquent {

	public $primaryKey = 'u_id';

	public function register($mobile, $pass){

		$this->u_phone = $mobile;
		$this->u_password = Hash::make($pasa);
		// check uniqueness of token
		$this->token = $this->getUniqueToken();

	}

	public function getUniqueToken(){
		$token = Str::random(32);
		if(User::where('token', '=', $token)->count() > 0){
			$this->checkTokenUniqueness()
		}
		return $token;
	}

	public function login(){
		
	}
}