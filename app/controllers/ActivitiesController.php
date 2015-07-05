<?php

class ActivitiesController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $key_word = Input::get('key', '');
        $hot = Input::get('hot', 0);
        $new = Input::get('new', 0);
        $site_id = Input::get('site', 1);

        $query = Activity::with('replys', 'signedUsers')->where('ac_status', '=', 1);
        if ($hot) {
            $query->orderBy('ac_read_count', 'desc');
        }
        if ($new) {
            $query->orderBy('created_at', 'desc');
        }
        if ($site_id) {
            $query->where('s_id', '=', $site_id);
        }
        if ($key_word) {
            $query->where('ac_content', 'LIKE', '%'.$key_word.'%');
        }
        $acts = $query->paginate(10);
        $list = [];
        foreach ($acts as $key => $act) {
            $list[] = $act->showInList();
        }
        $re = ['result' => 2000, 'data' => $list, 'info' => '活动获取成功'];

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
    public function store()
    {
        $token = Input::get('token');
        $u_id = Input::get('u_id');
        $imgToken = Input::get('img_token', '');
        $title = Input::get('title');
        $title = urldecode($title);
        $content = Input::get('content');
        $content = urldecode($content);
        $start = Input::get('start');
        $end = Input::get('end');
        $address = Input::get('address');
        $address = urldecode($address);
        $needData = Input::get('need_data', 0);
        $site = Input::get('site');
        $ex_user = Input::get('ex_user', 0);
        $needSupport = Input::get('need_supp', 0);
        $supportAmount = Input::get('supp_amt', 0);
        try {
            $user = User::chkUserByToken($token, $u_id);
            $act = new Activity();
            $act->ac_title = $title;
            $act->ac_status = 1;
            $act->ac_content = $content;
            $act->ac_begin_date = $start;
            $act->ac_end_date = $end;
            $act->s_id = $site;
            $act->ac_isex_signuser = $ex_user;
            $act->ac_creat_user = $user->u_id;
            $act->ac_address = $address;
            $act->ac_isdata = $needData;
            $act->ac_att_count = 0;
            $act->ac_sign_count = 0;
            $act->ac_read_count = 0;
            $act->ac_need_support = $needSupport;
            $act->ac_support_amount = $supportAmount;
            $act->addAct();
            if ($imgToken) {
                // save img
                $img = new Img('activity', $imgToken);
                $act->ac_pic_path = $img->getSavedImg($act->id, $act->ac_pic_path);
                $act->save();
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '活动添加成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
            if ($e->getCode() == 2) {
                if ($act->ac_id > 0) {
                    $act->delete();
                }
            }
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
        $act = Activity::with('replys', 'signedUsers')->where('ac_id', '=', $id)->where('ac_status', '=', 1)->first();
        if (!isset($act->ac_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您请求的活动不存在']);
        }
        try {
            $data = $act->showInList();
            $re = ['result' => 2000, 'data' => $data, 'info' => '操作成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }


    /**
     * add replies
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $act = Activity::find($id);
        if (!isset($act->ac_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您回复的活动不存在']);
        }
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', null);
        $content = Input::get('content');
        $content = urldecode($content);
        $reply = new ActivitiesReply();
        $reply->ac_id = $id;
        $reply->r_content = $content;
        $reply->r_status = 1;
        $reply->created_at = date('Y-m-d H:i:s');
        try {
            $this->user = User::chkUserByToken($token, $u_id);
            $reply->u_id = $this->user->u_id;
            $reply->addReply();
            $re = ['result' => 2000, 'data' => [], 'info' => '回复成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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

    /**
     * sign in to an activity
     * @author Kydz 2015-07-02
     * @param  int $id activity id
     * @return json     activity detail
     */
    public function sign($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', null);
        $comment = Input::get('comment', '');
        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $actSign = new ActivitiesSignUser();
            $actSign->ac_id = $id;
            $actSign->u_id = $user->u_id;
            $actSign->created_at = date('Y-m-d H:i:s');
            $actSign->s_status = 0;
            $actSign->sign_comment = $comment;
            $actSign->signUp($imgToken);

            $re = ['result' => 2000, 'data' => [], 'info' => '报名成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
            if ($e->getCode() == 2) {
                $actSign->delete();
            }
        }
        return Response::json($re);

    }

    /**
     * follow or unfollow an acitivty
     * @author Kydz 2015-07-05
     * @param  int $id act id
     * @return json     n/a
     */
    public function follow($id)
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token', '');
        $type = Input::get('type', 1);

        $act = Activity::find($id);
        if (!isset($act->ac_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '没有找到请求的活动']);
        }
        try {
            $user = User::chkUserByToken($token, $u_id);
            if ($type == 1) {
                $act->follow($user->u_id);
            } elseif ($type == 2) {
                $act->unfollow($user->u_id);
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '操作成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }

    /**
     * get all signed users, authorized creator only
     * @author Kydz 2015-07-06
     * @return json signed user list
     */
    public function signers($id)
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token', '');
        $act = Activity::with('signedUsers')->find($id);
        if ($act->ac_creat_user != $u_id) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您没有权限查看报名用户']);
        }
        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = [];
            foreach ($act->signedUsers as $key => $signedUser) {
                $data[] = $signedUser->showInList();
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取报名用户成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * make singed user valide
     * @author Kydz 2015-07-06
     * @param  int $id  activity id
     * @param  int $sid activity signed user ralation id (table - t_activities_sign_users)
     * @return json      n/a
     */
    public function confirmSignedUser($id, $sid)
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');
        $act = Activity::find($id);
        $actSignUser = ActivitiesSignUser::find($sid);
        if (!isset($act->ac_id) || $act->ac_creat_user != $u_id || !isset($actSignUser->r_id) || $actSignUser->ac_id != $id) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您没有权限操作用户报名审核']);
        }

        try {
            $user = User::chkUserByToken($token, $u_id);
            $actSignUser->confirm();
            $re = ['result' => 2000, 'data' => [], 'info' => '用户报名验证通过'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }
}
