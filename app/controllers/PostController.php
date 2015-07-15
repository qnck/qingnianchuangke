<?php

class PostController extends \BaseController
{

    private $_user = null;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = [];
        $p_id = Input::get('id', 0);
        $way = Input::get('way', 'up');
        $num = Input::get('num', 10);
        $following = Input::get('following', 0);
        $focused_user = Input::get('user', 0);
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');
        $keyWord = Input::get('key', '');
        try {
            $query = Post::with([
                'replys' => function ($q) {
                    $q->where('r_status', '=', 1);
                },
                'replys.user',
                'replys.user.school',
                'replys.toUser',
                'praises',
                'user',
                'user.school'
                ])->where('p_status', '=', 1);
            if ($following) {
                $user = User::chkUserByToken($token, $u_id);
                $query->join('attentions', function ($j) use ($user) {
                    $j->on('posts.u_id', '=', 'attentions.u_id')
                    ->where('attentions.u_fans_id', '=', $user->u_id);
                });
            }
            if ($focused_user) {
                $query->where('u_id', '=', $focused_user);
            }
            if ($keyWord) {
                $query->where('p_title', 'LIKE', '%'.$keyWord.'%');
            }
            if ($p_id) {
                if ($way == 'down') {
                    $query->where('p_id', '<', $p_id);
                    $query->orderBy('p_id', 'DESC');
                } elseif ($way == 'up') {
                    $query->where('p_id', '>', $p_id);
                    $query->orderBy('p_id', 'ASC');
                }
            } else {
                $query->orderBy('p_id', 'DESC');
            }
            $posts = $query->take($num)->get();
            if ($way == 'up') {
                $posts->sortByDesc('p_id');
            }
            foreach ($posts as $post) {
                $data[] = $post->showInList();
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '读取帖子成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => $data, 'info' => $e->getMessage()];
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
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $title = Input::get('title');
        $title = urldecode($title);
        $longitude = Input::get('longitude');
        $latitude = Input::get('latitude');
        $address = Input::get('address');
        $imgToken = Input::get('img_token', '');
        $site_id = 1;
        $post = new Post();
        $post->p_title = $title;
        $post->s_id = $site_id;
        $post->p_longitude = $longitude;
        $post->p_latitude = $latitude;
        $post->p_address = $address;
        try {
            $this->_user = User::chkUserByToken($token, $u_id);
            $post->u_id = $this->_user->u_id;
            $post->addPost($imgToken);
            $re = ['result' => 2000, 'data' => [], 'info' => '添加成功'];
        } catch (Exception $e) {
            $re = ['result' =>  2001, 'data' => [], 'info' => $e->getMessage()];
            if ($e->getCode() == 2) {
                $post->delete();
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
        $post = Post::with([
            'replys' => function ($query) {
                $query->where('r_status', '=', 1);
            },
            'replys.user',
            'replys.toUser',
            'praises.user',
            'user',
            'user.school'
        ])
        ->where('p_id', '=', $id)->where('p_status', '=', 1)->first();
        if (!isset($post->p_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '请求的帖子不存在']);
        }
        try {
            $data = $post->showInList();
            $re = ['result' => 2000, 'data' => $data, 'info' => '读取帖子成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }


    /**
     * Add reply to a post
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        if (!isset($post->p_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '您回复的帖子不存在']);
        }
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $to_user = Input::get('to', 0);
        $content = Input::get('content', '');
        $reply = new PostsReply();
        $reply->p_id = $id;
        $reply->r_content = $content;
        $reply->r_status = 1;
        $reply->to_u_id = $to_user;
        $reply->created_at = date('Y-m-d H:i:s');
        try {
            $this->_user = User::chkUserByToken($token, $u_id);
            $reply->u_id = $this->_user->u_id;
            $reply->addReply();
            $post->p_reply_count += 1;
            $post->save();
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
        
    }


    /**
     * Remove the specified resource softly.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $token = Input::get('token');
        $u_id = Input::get('u_id');
        $post = Post::find($id);
        try {
            User::chkUserByToken($token, $u_id);
            $post->disable();
            $re = ['result' =>2000, 'data' => [], 'info' => '删除成功'];
        } catch (Exception $e) {
            $re = ['result' =>2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * add/del praise
     * @author Kydz 2015-06-17
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function praise($id)
    {
        $token = Input::get('token');
        $u_id = Input::get('u_id');
        $type = Input::get('type');
        $post = Post::find($id);
        try {
            $user = User::chkUserByToken($token, $u_id);
            $result = 2000;
            if ($type == 1) {
                $post->addPraise();
                $praise = new PostsPraise();
                $praise->p_id = $id;
                $praise->u_id = $user->u_id;
                $praise->created_at = date('Y-m-d H:i:s');
                $praise->addPraise();
                $info = '点赞成功';
            } elseif ($type == 2) {
                $post->delPraise();
                $praise = PostsPraise::where('p_id', '=', $id)->where('u_id', '=', $user->u_id)->first();
                if (isset($praise->pp_id)) {
                    $praise->delete();
                }
                $info = '取消赞成功';
            } else {
                $result = 2001;
                $info = '操作失败';
            }
            $re = ['result' => $result, 'data' => [], 'info' => $info];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);

    }

    /**
     * disable reply
     * @author Kydz 2015-06-17
     * @param  int $id reply id
     * @return respose
     */
    public function disableReply($id)
    {
        $token = Input::get('token');
        $u_id = Input::get('u_id');
        $reply = PostsReply::find($id);
        try {
            User::chkUserByToken($token, $u_id);
            $reply->disable();
            $re = ['result' => 2000, 'data' => [], 'info' => ['评论删除成功']];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }
}
