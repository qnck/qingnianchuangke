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
            $data = $user->login();
            $re = ['data' => $data, 'result' => 2000, 'info' => '登陆成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'result' => 2001, 'info' => $e->getMessage()];
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
        $school_id = Input::get('school');
        $vCode = Input::get('vcode');
        $user = new User();
        $user->u_school_id = $school_id;
        $user->u_mobile = $mobile;
        $user->u_password = $pass;
        try {
            // verify vcode via phone
            $phone = new Phone($mobile);
            $phone->authVCode($vCode);
            $data = $user->register();
            $re = ['data' => $data, 'result' => 2000, 'info' => '注册成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'info' => $e->getMessage(), 'result' => 2001];
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
            return Response::json(['result' => 2001, 'data' => [], 'info' => '没有找到请求的用户']);
        }
        try {
            $data = $user->showDetail();
            $re = ['result' => 2000, 'data' => $data, 'info' => '读取用户成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }


    /**
     * add follow or unfollow.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $type = Input::get('type');
        $token = Input::get('token');
        $u_id = Input::get('u_id');

        $user = User::chkUserByToken($token, $u_id);
        try {
            $target = User::find($id);
            if ($type == 1) {
                User::follow($user, $target);
            } else {
                User::unfollow($user, $target);
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '操作成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
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
        $user->u_id = $id;
        $user->u_token = Input::get('token', null);
        $user->u_password = Input::get('pass', null);
        $user->u_age = Input::get('age', null);
        $user->u_name = Input::get('name', null);
        $user->u_sex = Input::get('sex', null);
        $user->u_school_id = Input::get('school_id', null);
        $user->u_prof = Input::get('profession', null);
        $user->u_degree = Input::get('degree', null);
        $user->u_entry_year = Input::get('in_year', null);
        $imgToken = Input::get('img_token', '');
        try {
            $user->updateUser($imgToken);
            $re = ['data' => [], 'result' => 2000, 'info' => '更新成功'];
        } catch (Exception $e) {
            $re = ['data' => [], 'result' => 2001, 'info' => $e->getMessage()];
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

    /**
     * get my followers
     * @author Kydz 2015-07-04
     * @param  int $id user id
     * @return json     followers
     */
    public function followers($id)
    {
        try {
            $user = User::with([
            'followers' => function ($q) {
                $q->where('u_status', '=', 1)->paginate(10);
            },
            ])->find($id);
            $data = $user->getFollowers();
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取粉丝成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取粉丝失败'];
        }
        return Response::json($re);
    }

    /**
     * get my followings
     * @author Kydz 2015-07-04
     * @param  int $id user id
     * @return json     followings
     */
    public function followings($id)
    {
        try {
            $user = User::with([
            'followings' => function ($q) {
                $q->where('u_status', '=', 1)->paginate(10);
            },
            ])->find($id);
            $data = $user->getFollowings();
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取关注的人成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取关注的人失败'];
        }
        return Response::json($re);
    }

    /**
     * follow or unfollow other users
     * @author Kydz
     * @param  int $id target id
     * @return json     n/a
     */
    public function follow($id)
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');
        $type = Input::get('type');
        $target = User::find($id);
        if (!isset($target->u_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您关注的用户不存在']);
        }
        if ($id == $u_id) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您不能关注自己']);
        }

        try {
            $user = User::chkUserByToken($token, $u_id);
            if ($type == 1) {
                $msg = '关注成功';
                User::follow($user, $target);
            } elseif ($type == 2) {
                $msg = '取消关注成功';
                User::unfollow($user, $target);
            }
            $re = ['result' => 2000, 'data' => [], 'info' => $msg];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * search user
     * @author Kydz
     * @return json user list
     */
    public function search()
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');
        $keyWord = Input::get('key');
        if (empty($keyWord)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '请输入搜索关键字']);
        }
        try {
            User::chkUserByToken($token, $u_id);
            $data = User::with(['school'])->where('u_name', 'LIKE', '%'.$keyWord.'%')->orWhere('u_id', 'LIKE', '%'.$keyWord.'%')->get();
            $list = [];
            foreach ($data as $key => $user) {
                $list[] = $user->showInList();
            }
            $re = ['result' => 2000, 'data' => $list, 'info' => '操作成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }
}
