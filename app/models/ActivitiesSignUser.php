<?php

/**
*
*/
class ActivitiesSignUser extends Eloquent
{

    public $primaryKey = 'r_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['activity' => $this->ac_id, 'user' => $this->u_id],
            ['activity' => 'required|digits_between:1,11', 'user' => 'required|digits_between:1,11']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }
    

    public function signUp($imgToken = '')
    {
        $chk = ActivitiesSignUser::where('ac_id', '=', $this->ac_id)->where('u_id', '=', $this->u_id)->count();
        if ($chk > 0) {
            throw new Exception("您已经报过名了", 1);
        }
        $this->baseValidate();
        $act = Activity::find($this->ac_id);
        if (!isset($act->ac_id)) {
            throw new Exception("没有获取到请求的活动", 1);
        }
        if ($act->ac_isdata == 1) {
            if (!$imgToken) {
                throw new Exception("请上传活动资料", 1);
            }
        }
        if (!$this->save()) {
            throw new Exception("活动报名失败", 1);
        }
        if ($imgToken) {
            // save img
            $img = new Img('activitySignUser', $imgToken);
            $this->sign_data_path = $img->getSavedImg($this->r_id, $this->sign_data_path);
            $this->save();
        }
        $act->ac_sign_count += 1;
        if (!$act->save()) {
            throw new Exception("修改活动数据失败", 1);
        }

        return true;
    }

    public function showInList()
    {
        $user = null;
        if (isset($this->user->u_id)) {
            $user = $this->user->showInList();
        }
        $imgs = explode(',', $this->sign_data_path);
        return ['id' => $this->r_id, 'user' => $user, 'sign_time' => $this->created_at->format('Y-m-d H:i:s'), 'imgs' => $imgs];
    }

    public function confirm()
    {
        $this->s_status = 1;
        if (!$this->save()) {
            throw new Exception("确认用户失败", 1);
        }
        return true;
    }

    // relation
    //
    public function activity()
    {
        return $this->belongsTo('Activity', 'ac_id', 'ac_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}
