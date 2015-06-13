<?php

/**
* 
*/
class VerificationCode extends Eloquent{

	public $primaryKey = 'v_id';

	public function verifiable()
    {
        return $this->morphTo();
    }

    public function generateCode(){
		return Str::random(6);
	}

	public function addCode(){
		$this->v_code = $this->generateCode();

			
	}
}