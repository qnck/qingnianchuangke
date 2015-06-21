<?php

/**
* 
*/
class Activity extends Eloquent{


	public $primaryKey = 'ac_id';
	public $timestamps = false;

	public function baseValidate(){
		$validator = Validator::make(
			['title' => $this->ac_title, 'content' => $this->ac_content, 'start' => $this->ac_begin_date, 'end' => $this->ac_end_date, 'user' => $this->ac_creat_user, 'status' => $this->ac_status, 'address' => $this->ac_address, 'needData' => $this->ac_isdata],
			['title' => 'required|max:200', 'content' => 'required', 'start' => 'required', 'end' => 'required', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'address' => 'required', 'needData' => 'required']
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);
		}else{
			return true;
		}
	}

	public function user(){
		return $this->hasOne('user', 'u_id', 'ac_creat_user');
	}

	public function addAct(){
		$this->baseValidate();
		if(!$this->save()){
			throw new Exception("活动添加失败", 1);
		}else{
			return true;
		}
	}

	public function signedUsers(){
		return $this->hasManyThrough('User', 'ActivitiesSignUser', 'ac_id', 'u_id');
	}

	public function showInList(){
		$signedUsers = [];
		if(isset($this->signedUsers)){
			foreach ($this->signedUsers as $key => $user) {
				$signedUsers[] = $user->showInList();
			}
		}
		return ['title' => $this->ac_title, 'content' => $this->ac_content, 'address' => $this->ac_address, 'pic' => $this->ac_pic_path, 'att_count' => $this->ac_att_count, 'sign_count' => $this->ac_sign_count, 'create_user' => $this->user->showInList(), 'signedUsers' => $signedUsers];
	}
}