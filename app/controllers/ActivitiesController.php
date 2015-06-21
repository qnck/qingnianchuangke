<?php

class ActivitiesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$acts = Activity::with(['user', 'signedUsers'])->where('ac_status', '=', 1)->paginate(10);
		$list = [];
		foreach ($acts as $key => $act) {
			$list[] = $act->showInList();
		}
		$re = ['result' => true, 'data' => $list, 'info' => '活动获取成功'];

		return Response::json($re);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		$token = Input::get('token');
		$title = Input::get('title');
		$title = urldecode($title);
		$content = Input::get('content');
		$content = urldecode($content);
		$start = Input::get('start');
		$end = Input::get('end');
		$address = Input::get('address');
		$address = urldecode($address);
		$needData = Input::get('needData');
		try {
			$user = User::chkUserByToken($token);
			$act = new Activity();
			$act->ac_title = $title;
			$act->ac_status = 0;
			$act->ac_content = $content;
			$act->ac_begin_date = $start;
			$act->ac_end_date = $end;
			$act->ac_creat_user = $user->u_id;
			$act->ac_address = $address;
			$act->ac_isdata = $needData;
			$act->addAct();
			$re = ['result' => true, 'data' => [], 'info' => '活动添加成功'];
		} catch (Exception $e) {
			$re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
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
		$act = Activity::with('signedUsers', 'user')->where('ac_id', '=', $id)->where('ac_status', '=', 1)->first();
		if(!isset($act->ac_id)){
			return Response::json(['result' => false, 'data' => [], 'info' => '您请求的活动不存在']);
		}
		try {
			$data = $act->showInList();
			$re = ['result' => true, 'data' => $data, 'info' => '操作成功'];
		} catch (Exception $e) {
			$re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
		}
		return Response::json($re);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
		$act = Activity::find($id);
		if(!isset($act->ac_id)){
			return Response::json(['result' => false, 'data' => [], 'info' => '您回复的活动不存在']);
		}
		$token = Input::get('token');
		$content = Input::get('content');
		$content = urldecode($content);
		$reply = new ActivityReply();
		$reply->ac_id = $id;
		$reply->r_content = $content;
		$reply->r_status = 1;
		$reply->created_at = date('Y-m-d H:i:s');
		try {
			$this->user = User::chkUserByToken($token);
			$reply->u_id = $this->user->u_id;
			$reply->addReply();
			$re = ['result' => true, 'data' => [], 'info' => '回复成功'];
		} catch (Exception $e) {
			$re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
		}
		return Response::json($re);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	public function sign($id){
		$token = Input::get('token');
		try {
			$user = User::chkUserByToken($token);
			$actSign = new ActivitiesSignUser();
			$actSign->ac_id = $id;
			$actSign->u_id = $user->u_id;
			// todo file path
			$actSign->signUp();
			$re = ['result' => true, 'data' => [], 'info' => '报名成功'];
		} catch (Exception $e) {
			$re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
		}
		return Response::json($re);

	}


}
