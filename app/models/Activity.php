<?php

/**
*
*/
class Activity extends Eloquent
{
    public $primaryKey = 'ac_id';
    public $timestamps = false;

    public function baseValidate()
    {
        $validator = Validator::make(
            ['title' => $this->ac_title, 'content' => $this->ac_content, 'start' => $this->ac_begin_date, 'end' => $this->ac_end_date, 'user' => $this->ac_creat_user, 'status' => $this->ac_status, 'address' => $this->ac_address, 'needData' => $this->ac_isdata, 'site' => $this->s_id],
            ['title' => 'required|max:200', 'content' => 'required', 'start' => 'required', 'end' => 'required', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'address' => 'required', 'needData' => 'required', 'site' => 'required|digits_between:1,11']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function creator()
    {
        return $this->belongsTo('user', 'ac_creat_user', 'u_id');
    }

    public function addAct()
    {
        $this->baseValidate();
        $this->created_at = date('Y-m-d H:i:s');
        if (!$this->save()) {
            throw new Exception("活动添加失败", 1);
        } else {
            return true;
        }
    }

    public function follow($u_id)
    {
        $chk = ActivitiesFollow::where('ac_id', '=', $this->ac_id)->where('u_id', '=', $u_id)->count();
        if ($chk > 0) {
            throw new Exception("您已经关注过了", 1);
        }
        $actf = new ActivitiesFollow();
        $actf->ac_id = $this->ac_id;
        $actf->u_id = $u_id;
        $actf->created_at = date('Y-m-d H:i:s');

        if (!$actf->save()) {
            throw new Exception('关注活动失败', 1);
        }

        $this->ac_att_count += 1;
        if (!$this->save()) {
            throw new Exception("关注活动失败", 1);
        }

        return true;
    }

    public function unfollow($u_id)
    {
        $actf = ActivitiesFollow::where('ac_id', '=', $this->ac_id)->where('u_id', '=', $u_id)->first();

        if (!isset($actf->ac_id)) {
            return true;
        }

        if (!$actf->delete()) {
            throw new Exception('取消关注活动失败', 1);
        }

        $this->ac_att_count -= 1;
        if ($this->ac_att_count <= 0) {
            $this->ac_att_count = 0;
        }
        if (!$this->save()) {
            throw new Exception("取消关注活动失败", 1);
        }

        return true;
    }

    public function showInList()
    {
        $signedUsers = null;
        if (isset($this->signedUsers)) {
            foreach ($this->signedUsers as $key => $signed) {
                $signedUsers[] = $signed->showInList();
            }
        }
        $replys = null;
        if (isset($this->replys)) {
            foreach ($this->replys as $key => $reply) {
                $replys[] = $reply->showInList();
            }
        }
        if ($this->ac_pic_path) {
            $tmp = explode(',', $this->ac_pic_path);
            $this->ac_pic_path = array_pop($tmp);
        }
        return ['id' => $this->ac_id, 'title' => $this->ac_title, 'content' => $this->ac_content, 'address' => $this->ac_address, 'pic' => $this->ac_pic_path, 'att_count' => $this->ac_att_count, 'sign_count' => $this->ac_sign_count, 'create_user' => $this->creator->showInList(), 'replys' => $replys, 'signedUsers' => $signedUsers];
    }

    // eloquent relations
    public function replys()
    {
        return $this->hasMany('ActivitiesReply', 'ac_id', 'ac_id');
    }

    public function signedUsers()
    {
        return $this->hasMany('ActivitiesSignUser', 'ac_id', 'ac_id');
    }

    public function followers()
    {
        return $this->belongsToMany('User', 'activities_follows', 'u_id', 'u_id');
    }
}
