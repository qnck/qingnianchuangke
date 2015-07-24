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


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
            if ($u_id < $friend) {
                $u_id_1 = $u_id;
                $u_id_2 = $friend;
            } else {
                $u_id_1 = $friend;
                $u_id_2 = $u_id;
            }
            $userFriend = UsersFriend::where('u_id_1', '=', $u_id_1)->where('u_id_2', '=', $u_id_2)->first();
            if (empty($userFriend->t_id)) {
                throw new Exception("无法确认不存在的好友", 3001);
            }
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
            if ($u_id < $friend) {
                $u_id_1 = $u_id;
                $u_id_2 = $friend;
            } else {
                $u_id_1 = $friend;
                $u_id_2 = $u_id;
            }
            $userFriend = UsersFriend::where('u_id_1', '=', $u_id_1)->where('u_id_2', '=', $u_id_2)->first();
            if (empty($userFriend->t_id)) {
                throw new Exception("无法删除不存在的好友", 3001);
            }
            $userFriend->remove();
            $re = ['result' => 2000, 'data' => [], 'info' => '删除好友成功'];
        } catch (Exception $e) {
            $re = ['result' => 3001, 'data' => [], 'info' => '删除好友失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }
}
