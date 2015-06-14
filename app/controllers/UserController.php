<?php

class UserController extends \BaseController {

	/**
	 * sign in
	 *
	 * @return Response
	 */
	public function index(){
		$mobile = Input::get('mobile');
		$pass = Input::get('pass');
		$user = new User();
		$user->u_mobile = $mobile;
		$user->u_password = $pass;
		try {
			$re = ['data' => ['token' => $user->login()], 'error' => false, 'info' => '登陆成功'];
		} catch (Exception $e) {
			$re = ['data' => [], 'error' => true, 'info' => $e->getMessage()];
		}
		return Response::json($re);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(){
	}


	/**
	 * sign up
	 *
	 * @return Response
	 */
	public function store(){
		$mobile = Input::get('mobile');
		$pass = Input::get('pass');
		$vCode = Input::get('vcode');
		$user = new User();
		$user->u_mobile = $mobile;
		$user->u_password = $pass;
		try {
			// verify vcode via phone
			$phone = new Phone($mobile);
			$phone->authVCode($vCode);			
			$re = ['data' => ['token' => $user->register()], 'error' => false, 'info' => '注册成功'];
		} catch (Exception $e) {
			$re = ['data' => [], 'info' => $e->getMessage(), 'error' => true];
		}
		return Response::json($re);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
		//
	}


	/**
	 * update
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id){
		$user = new User();
		$user->u_token = $id;
		$user->u_nickname = Input::get('nickname');
		$user->u_age = Input::get('age');
		$user->u_name = Input::get('name');
		$user->u_sex = Input::get('sex');
		$user->u_head_photo = Input::get('head_photo');
		$user->u_pid = Input::get('pid');
		$user->u_id_photo = Input::get('id_photo');
		$user->u_school_name = Input::get('school_name');
		$user->u_student_num = Input::get('student_num');
		$user->u_student_photo = Input::get('student_photo');
		$user->u_address = Input::get('address');
		try {
			$user->updateUser();
			$re = ['data' => [], 'error' => false, 'info' => '更新成功'];
		} catch (Exception $e) {
			$re = ['data' => [], 'error' => true, 'info' => $e->getMessage()];
		}
		return Response::json($re);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id){
		//
	}


}
