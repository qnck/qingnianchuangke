<?php

/**
*
*/
class Post extends Eloquent
{

    public $primaryKey = 'p_id';
    public $timestamps = false;

    public function baseValidate()
    {
        $validator = Validator::make(
            ['title' => $this->p_title, 'user' => $this->u_id, 'status' => $this->p_status, 'site' => $this->s_id],
            ['title' => 'required|max:140', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'site' => 'required|digits_between:1,11']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    /**
     * add new post
     * @author Kydz 2015-06-17
     */
    public function addPost($imgToken)
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->p_status = 1;
        $this->baseValidate();
        if (!$this->save()) {
            throw new Exception("添加帖子失败", 1);
        }
        if ($imgToken) {
            $img = new Img('post', $imgToken);
            $this->p_content = $img->getSavedImg($this->p_id, $this->p_content);
            $this->save();
        }

        return true;
    }

    /**
     * disable post
     * @author Kydz 2015-06-17
     * @return n/a
     */
    public function disable()
    {
        $this->baseValidate();
        $this->p_status = 0;
        $this->save();
    }

    /**
     * enable post
     * @author Kydz 2015-06-17
     * @return n/a
     */
    public function enable()
    {
        $this->baseValidate();
        $this->p_status = 1;
        $this->save();
    }

    /**
     * add priase count
     * @author Kydz 2015-06-17
     */
    public function addPraise()
    {
        $this->baseValidate();
        $this->p_praise += 1;
        $this->save();
    }

    /**
     * minus priase count
     * @author Kydz 2015-06-17
     * @return n/a
     */
    public function delPraise()
    {
        $this->baseValidate();
        $this->p_praise -= 1;
        $this->save();
    }

    /**
     * format data used in list
     * @author Kydz 2015-06-17
     * @return array list row
     */
    public function showInList()
    {
        $replys = null;
        if (isset($this->replys)) {
            foreach ($this->replys as $reply) {
                $replys[] = $reply->showInList();
            }
        }
        $priases = null;
        if (isset($this->praises)) {
            foreach ($this->praises as $praise) {
                $priases[] = $praise->showInList();
            }
        }
        $imglist = null;
        if (isset($this->p_content)) {
            $imglist = explode(',', $this->p_content);
        }
        $user = null;
        if (isset($this->user)) {
            $user = $this->user->showInList();
        }
        $data = ['id' => $this->p_id, 'title' => $this->p_title, 'post_time' => $this->created_at->format('Y-m-d H:i:s'), 'user' => $user, 'replys' => $replys, 'reply_count' => $this->p_reply_count, 'priases' => $priases, 'praise_count' => $this->p_praise, 'imgs' => $imglist];
        return $data;
    }

    // eloquent relations
    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function replys()
    {
        return $this->hasMany('PostsReply', 'p_id', 'p_id');
    }

    public function praises()
    {
        return $this->hasMany('PostsPraise', 'p_id', 'p_id');
    }
}
