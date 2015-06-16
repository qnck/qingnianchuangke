<?php

class User extends Eloquent {

	public $primaryKey = 'u_id';

	/**
	 * validate base info
	 * @author Kydz 2015-06-14
	 * @return bool
	 */
	public function baseValidate(){
		$validator = Validator::make(
				['mobile' => $this->u_mobile, 'pass' => $this->u_password],
				['mobile' => 'required|digits:11', 'pass' => 'required|alpha_dash|min:6']
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);
		}else
			return true;
	}

	/**
	 * get unique token
	 * @author Kydz 2015-06-14
	 * @return string token
	 */
	public function getUniqueToken(){
		$token = Str::random(32);
		if(User::where('u_token', '=', $token)->count() > 0) $this->checkTokenUniqueness();
		return $token;
	}

	/**
	 * register user
	 * @author Kydz 2015-06-14
	 * @return string user token
	 */
	public function register(){
		$this->baseValidate();
		// generate token
		$this->u_token = $this->getUniqueToken();
		// chcek if mobile exsits
		if(User::where('u_mobile', '=', $this->u_mobile)->count() > 0){
			throw new Exception("手机号码已被使用", 1);
		}
		$this->u_password = Hash::make($this->u_password);
		$this->u_status = 1;
		$this->save();
		return $this->u_token;
	}

	/**
	 * log user in
	 * @author Kydz 2015-06-14
	 * @return sting user token
	 */
	public function login(){
		$this->baseValidate();
		$user = User::where('u_mobile', '=', $this->u_mobile)->first();
		if(!isset($user->u_id)){
			throw new Exception("没有找到请求的用户", 1);
		}
		if(!Hash::check($this->u_password, $user->u_password)){
			throw new Exception("密码错误", 1);
		}else{
			return $user->u_token;
		}
	}

	/**
	 * update user info
	 * @author Kydz 2015-06-14
	 * @return bool
	 */
	public function updateUser(){
		$validator = Validator::make(
			[
				'nickname' => $this->u_nickname,
				'age' => $this->u_age,
				'name' => $this->u_name,
				'sex' => $this->u_sex,
				'head_photo' => $this->u_head_photo,
				'pid' => $this->u_pid,
				'id_photo' => $this->u_id_photo,
				'school_name' => $this->u_school_name,
				'student_num' => $this->u_student_num,
				'student_photo' => $this->u_student_photo,
				'address' => $this->u_address,
			],
			[
				'nickname' => 'sometimes|max:32',
				'age' => 'sometimes|digits_between:1,3',
				'name' => 'sometimes|max:5',
				'sex' => 'sometimes|digits:1',
				'head_photo' => 'sometimes',
				'pid' => 'sometimes',
				'id_photo' => 'sometimes',
				'school_name' => 'sometimes',
				'student_num' => 'sometimes|alpha_num',
				'student_photo' => 'sometimes',
				'address' => 'sometimes',
			]
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);			
		}
		$user = User::where('u_token', '=', $this->u_token)->first();
		if(!isset($user->u_id)){
			throw new Exception("没有找到请求的用户", 1);
		}

		isset($this->u_nickname) ? $user->u_nickname = $this->u_nickname : '';
		isset($this->u_age) ? $user->u_age = $this->u_age : '';
		isset($this->u_name) ? $user->u_name = $this->u_name : '';
		isset($this->u_sex) ? $user->u_sex = $this->u_sex : '';
		isset($this->u_head_photo) ? $user->u_head_photo = $this->u_head_photo : '';
		isset($this->u_pid) ? $user->u_pid = $this->u_pid : '';
		isset($this->u_id_photo) ? $user->u_id_photo = $this->u_id_photo : '';
		isset($this->u_school_name) ? $user->u_school_name = $this->u_school_name : '';
		isset($this->u_student_num) ? $user->u_student_num = $this->u_student_num : '';
		isset($this->u_student_photo) ? $user->u_student_photo = $this->u_student_photo : '';
		isset($this->u_address) ? $user->u_address = $this->u_address : '';
		if(!$user->save()){
			throw new Exception("更新用户信息失败", 1);
		}else{
			return true;			
		}
	}

	public function showInList(){
		$data = [];
		$data['name'] = $this->u_nickname;
		$data['head_img'] = $this->u_head_img;
		$data['id'] = $this->u_id;
		return $data;
	}

	public function getPost(){
		return $this->hasMany('Post');
	}
}