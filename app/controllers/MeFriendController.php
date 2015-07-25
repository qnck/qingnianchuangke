<?php

class MeFriendController extends \BaseController {

    /**
     * Friends list
     *
     * @return Response
     */
    public function index()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $ver = Input::get('ver', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);

            // check changes
            $sum1 = DB::table('users_friends')->join('users', function ($j) use ($u_id) {
                $j->on('users_friends.u_id_1', '=', 'users.u_id')->where('users_friends.t_inviter', '=', 2)->where('users_friends.u_id_2', '=', $u_id);
            })->sum('users.u_change');

            $sum2 = DB::table('users_friends')->join('users', function ($j) use ($u_id) {
                $j->on('users_friends.u_id_2', '=', 'users.u_id')->where('users_friends.t_inviter', '=', 1)->where('users_friends.u_id_1', '=', $u_id);
            })->sum('users.u_change');

            $sum = $sum1 + $sum2;

            if ($ver >= $sum) {
                return Response::json(['result' => 2000, 'data' => ['ver' => $ver], 'info' => '获取我的好友列表成功']);
            }
            
            $data = $this->getUserList($u_id, 2);

            $re = ['result' => 2000, 'data' => $data, 'info' => '获取我的好友列表成功', 'ver' => $sum];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '获取我的好友列表失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function indexInvite()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $ver = Input::get('ver', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = $this->getUserList($u_id, 1);
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取好友邀请列表成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '获取好友邀请列表失败:'.$e->getMessage()];
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
        //
    }


    /**
     * add new friends
     *
     * @return Response
     */
    public function invite()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $friend = Input::get('friend', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $userFriend = new UsersFriend();
            $userFriend->u_id_1 = $u_id;
            $userFriend->u_id_2 = $friend;
            $userFriend->t_inviter = 1;
            $userFriend->invite($u_id);
            $re = ['result' => 2000, 'data' => [], 'info' => '邀请好友成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '邀请好友失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }

    public function check()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $friend = Input::get('friend', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $friend = User::find($friend);
            if (empty($friend->u_id)) {
                throw new Exception("你查找的用户不存在", 3001);
            }
            $userFriend = UsersFriend::findLinkById($u_id, $friend->u_id);
            $friendInfo = $friend->showDetail();
            $data = ['user_info' => $friendInfo, 'is_friend' => $userFriend->t_status];
            $re = ['result' => 2000, 'data' => $data, 'info' => '好友关系检测成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '好友关系检测失败:'.$e->getMessage()];
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
        //
    }


    /**
     * confirm friend
     *
     * @param  int  $id
     * @return Response
     */
    public function confirm()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $friend = Input::get('friend', 0);
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $userFriend = UsersDetails::findLinkById($u_id, $friend);
            if ($userFriend->t_status == 2) {
                throw new Exception("你们已经是好友了", 3001);
            }
            if (($userFriend->t_inviter == 1 && $u_id == $userFriend->u_id_1) || ($userFriend->t_inviter == 2 && $u_id == $userFriend->u_id_2)) {
                throw new Exception("您不能自己确认好友邀请", 3001);
            }
            $userFriend->confirm();
            $re = ['result' => 2000, 'data' => [], 'info' => '确认好友成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '确认好友失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }


    /**
     * remove friend
     *
     * @param  int  $id
     * @return Response
     */
    public function remove()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $friend = Input::get('friend', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $userFriend = UsersFriend::findLinkById($u_id, $friend);
            $userFriend->remove();
            $re = ['result' => 2000, 'data' => [], 'info' => '删除好友成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '删除好友失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function removeInvite()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $friend = Input::get('friend', 0);
        $re = ['result' => 2000, 'data' => [], 'info' => '删除好友邀请成功'];
        try {
            $user = User::chkUserByToken($token, $u_id);
            $userFriend = UsersFriend::findLinkById($u_id, $friend);
            if ($userFriend->t_status == 1) {
                $userFriend->remove();
            }
        } catch (Exception $e) {
            if ($e->getCode() == 3001) {
                $re = ['result' => 3001, 'data' => [], 'info' => '删除好友邀请失败:'.$e->getMessage()];
            }
        }
        return Response::json($re);
    }

    private function getUserList($u_id, $status = 2)
    {
        $list1 = UsersFriend::where('t_status', '=', $status)->where('t_inviter', '=', 1)->where('u_id_1', '=', $u_id)->with(['user2', 'user2.school'])->get();
        $list2 = UsersFriend::where('t_status', '=', $status)->where('t_inviter', '=', 2)->where('u_id_2', '=', $u_id)->with(['user1', 'user1.school'])->get();
        $data = [];
        foreach ($list1 as $key => $user) {
            $data[] = $user->user2->showInList();
        }
        foreach ($list2 as $key => $user) {
            $data[] = $user->user1->showInList();
        }
        return $data;
    }
}
