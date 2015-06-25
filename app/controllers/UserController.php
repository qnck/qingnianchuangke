<?php

class UserController extends \BaseController
{

    /**
     * sign in
     *
     * @return Response
     */
    public function index()
    {
        $mobile = Input::get('mobile');
        $pass = Input::get('pass');
        $user = new User();
        $user->u_mobile = $mobile;
        $user->u_password = $pass;
        try {
            $data = ['token' => $user->login()];
            $re = ['data' => [$data], 'result' => true, 'info' => '登陆成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'result' => false, 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
    }


    /**
     * sign up
     *
     * @return Response
     */
    public function store()
    {
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
            $data = ['token' => $user->register()];
            $re = ['data' => [$data], 'result' => true, 'info' => '注册成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'info' => $e->getMessage(), 'result' => false];
        }
        return Response::json($re);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!isset($user->u_id)) {
            return Response::json(['result' => false, 'data' => [], 'info' => '没有找到请求的用户']);
        }
        try {
            $data = $user->showDetail();
            $re = ['result' => true, 'data' => $data, 'info' => '读取用户成功'];
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
    public function edit($id)
    {
        $type = Input::get('type');
        $token = Input::get('token');

        $user = User::chkUserByToken($token);
        try {
            $target = User::find($id);
            if ($type == 1) {
                User::follow($user, $target);
            } else {
                User::unfollow($user, $target);
            }
            $re = ['result' => true, 'data' => [], 'info' => '操作成功'];
        } catch (Exception $e) {
            $re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
        }
    }


    /**
     * update
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $user = new User();
        $user->u_token = $id;
        $user->u_password = Input::get('pass', null);
        $user->u_nickname = Input::get('nickname', null);
        $user->u_age = Input::get('age', null);
        $user->u_name = Input::get('name', null);
        $user->u_sex = Input::get('sex', null);
        $user->u_identity_number = Input::get('identity_number', null);
        $user->u_school_name = Input::get('school_name', null);
        $user->u_student_number = Input::get('student_number', null);
        $user->u_address = Input::get('address', null);
        try {
            $user->updateUser();
            $re = ['data' => [], 'result' => true, 'info' => '更新成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'result' => false, 'info' => $e->getMessage()];
        }
        return Response::json($re);
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

    public function me()
    {
        $token = Input::get('token', '');
        try {
            $user = User::chkUserByToken($token);
            $user = User::with('bankCards.bank', 'contact')->find($user->u_id);
            $userInfo = $user->showDetail();
            $cards = $user->showBankCards();
            $contact = $user->showContact();
            $data = ['user_info' => $userInfo, 'cards' => $cards, 'contact' => $contact];
            $re = ['result' => true, 'data' => $data, 'info' => '获取用户成功'];
        } catch (Exception $e) {
            $re = ['result' => false, 'data'=> [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }
}
