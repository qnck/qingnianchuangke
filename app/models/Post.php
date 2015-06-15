<?php

/**
* 
*/
class Post extends Eloquent{

	public function baseVlidate(){
		$validator = Validate::make(
			['title' => $this->p_title, 'user' => $this->u_id, 'status' => $this->p_status, 'site' => $this->s_id],
			['title' => 'required', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'site' => 'required|digits_between:1,11']
			);
		if($validator->fails()){
			$msg = $validator->meesages();
			throw new Exception($msg->first(), 1);			
		}else{
			return true;
		}
	}

	public function repliable(){
		
	}
}