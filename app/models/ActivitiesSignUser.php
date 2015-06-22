<?php

/**
* 
*/
class ActivitiesSignUser extends Eloquent{

	public $primaryKey = 'r_id';
	public $timestamps = false;

	private function baseValidate(){
		$validator = Validator::make(
			['activity' => $this->ac_id, 'user' => $this->u_id],
			['activity' => 'required|digits_between:1,11', 'user' => 'required|digits_between:1,11']
		);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);
		}else{
			return true;
		}
	}
	
	public function activity(){
		return $this->belongsTo('Activity', 'ac_id', 'ac_id');
	}

	public function user(){
		return $this->belongsTo('User', 'u_id', 'u_id');
	}

	public function signUp(){
		$chk = ActivitiesSignUser::where('ac_id', '=', $this->ac_id)->where('u_id', '=', $this->u_id)->first();
		if(isset($chk->r_id)){
			throw new Exception("您已经报过名了", 1);			
		}
		$this->baseValidate();
		$act = Activity::find($this->ac_id);
		if(!isset($act->ac_id)){
			throw new Exception("没有获取到请求的活动", 1);
		}
		if($act->ac_isdata == 1){
			if(!$this->sign_data_path){
				throw new Exception("请上传活动资料", 1);
			}
		}
		if(!$this->save()){
			throw new Exception("活动报名失败", 1);
		}else{
			return true;
		}
	}

	public function showInList(){
		$user = [];
		if(isset($this->user->u_id)){
			$user = $this->user->showInList();
		}
		return ['id' => $this->r_id, 'user' => $user, 'sign_time' => $this->created_at->format('Y-m-d H:i:s')];
	}
}