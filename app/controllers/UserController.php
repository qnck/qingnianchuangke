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
        try {
            // AES crypt
            $pass = Tools::qnckDecrytp($pass);

            $user = new User();
            $user->u_mobile = $mobile;
            $user->u_password = $pass;
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
        try {
            // AES crypt
            $pass = Tools::qnckDecrytp($pass);

            $user = new User();
            $user->u_school_id = $school_id;
            $user->u_mobile = $mobile;
            $user->u_password = $pass;
            
            // verify vcode via phone
            $phone = new Phone($mobile);
            $phone->authVCode($vCode);
            $data = $user->register();
            // add user wallet
            $wallet = new UsersWalletBalances();
            $wallet->u_id = $user->u_id;
            $wallet->w_balance = 0.00;
            $wallet->w_freez = 0.00;
            $wallet->save();
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
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $keyWord = Input::get('key', '');
        $school = Input::get('school', 0);
        $range = Input::get('range', 0);
        $lat = Input::get('lat', 0);
        $lng = Input::get('lng', 0);
        $perPage = Input::get('per_page', 30);
        try {
            User::chkUserByToken($token, $u_id);
            $query = User::with(['school'])->where('u_id', '<>', $u_id);

            if ($keyWord) {
                $query = $query->where(function ($q) use ($keyWord) {
                    $q->where('u_name', 'LIKE', '%'.$keyWord.'%')->orWhere('u_mobile', '=', $keyWord)->orWhere('u_nickname', 'LIKE', '%'.$keyWord.'%');
                });
            }

            if ($school > 0) {
                $query = $query->where('u_school_id', '=', $school);
            }

            if ($range) {
                if (!$lat || !$lng) {
                    throw new Exception("请传入有效的经纬度", 1);
                }
                $distance = ['0' => 0.0, '1' => 0.5, '2' => 1.0, '3' => 1.5, '4' => 3.0, '5' => 5.0];
                
                if (!array_key_exists($range, $distance)) {
                    throw new Exception("请传入有效的距离档位", 1);
                }
                $user_ids = User::filterByDistance($lat, $lng, $distance[$range]);
                $query = $query->whereIn('u_id', $user_ids);
            }

            $data = $query->paginate($perPage);
            $list = [];
            foreach ($data as $key => $user) {
                $list[] = $user->showInList();
            }
            $re = Tools::reTrue('搜索用户成功', $list, $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '搜索用户失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
