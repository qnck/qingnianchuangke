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
        $following = Input::get('following', 0);
        $token = Input::get('token', '');
        try {
            $query = Post::with([
                'replys' => function ($q) {
                    $q->where('r_status', '=', 1);
                },
                'praises.user',
                'user'
                ])->where('p_status', '=', 1);
            if ($following) {
                $user = User::chkUserByToken($token);
                $query->join('attentions', function ($j) use ($user) {
                    $j->on('posts.u_id', '=', 'attentions.u_id')
                    ->where('attentions.u_fans_id', '=', $user->u_id);
                });
            }
            $posts = $query->paginate(10);
            foreach ($posts as $post) {
                $data[] = $post->showInList();
            }
            $re = ['result' => true, 'data' => $data, 'info' => '读取帖子成功'];
        } catch (Exception $e) {
            $re = ['result' => false, 'data' => $data, 'info' => $e->getMessage()];
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
        $token = Input::get('token');
        $title = Input::get('title');
        $title = urldecode($title);
        $longitude = Input::get('longitude');
        $latitude = Input::get('latitude');
        $address = Input::get('address');
        $imgToken = Input::get('imgToken', '');
        $site_id = 1;
        $post = new Post();
        $post->p_title = $title;
        $post->s_id = $site_id;
        $post->p_longitude = $longitude;
        $post->p_latitude = $latitude;
        $post->p_address = $address;
        try {
            $this->_user = User::chkUserByToken($token);
            $post->u_id = $this->_user->u_id;
            $post->addPost();
            $re = ['result' => true, 'data' => [], 'info' => '添加成功'];
            if ($imgToken) {
                $img = new Img('post', $imgToken);
                $imgs = $img->save($post->p_id);
                $post->p_content = implode(',', $imgs);
                $post->save();
            }
        } catch (Exception $e) {
            $re = ['result' =>  false, 'data' => [], 'info' => $e->getMessage()];
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
            'user'])
        ->where('p_id', '=', $id)->where('p_status', '=', 1)->first();
        if (!isset($post->p_id)) {
            return Response::json(['result' => false, 'data' => [], 'info' => '请求的帖子不存在']);
        }
        try {
            $data = $post->showInList();
            $re = ['result' => true, 'data' => $data, 'info' => '读取帖子成功'];
        } catch (Exception $e) {
            $re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
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
            return Response::json(['result' => false, 'data' => [], 'info' => '您回复的帖子不存在']);
        }
        $token = Input::get('token');
        $content = Input::get('content');
        $content = urldecode($content);
        $reply = new PostsReply();
        $reply->p_id = $id;
        $reply->r_content = $content;
        $reply->r_status = 1;
        $reply->created_at = date('Y-m-d H:i:s');
        try {
            $this->_user = User::chkUserByToken($token);
            $reply->u_id = $this->_user->u_id;
            $reply->addReply();
            $post->p_reply_count += 1;
            $post->save();
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
        $post = Post::find($id);
        try {
            User::chkUserByToken($token);
            $post->disable();
            $re = ['result' =>true, 'data' => [], 'info' => '删除成功'];
        } catch (Exception $e) {
            $re = ['result' =>false, 'data' => [], 'info' => $e->getMessage()];
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
        $type = Input::get('type');
        $post = Post::find($id);
        try {
            $user = User::chkUserByToken($token);
            $result = true;
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
                $result = false;
                $info = '操作失败';
            }
            $re = ['result' => $result, 'data' => [], 'info' => $info];
        } catch (Exception $e) {
            $re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
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
        $reply = PostsReply::find($id);
        try {
            User::chkUserByToken($token);
            $reply->disable();
            $re = ['result' => true, 'data' => [], 'info' => ['评论删除成功']];
        } catch (Exception $e) {
            $re = ['result' => false, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }
}