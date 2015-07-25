<?php
/**
*
*/
class UsersFriend extends Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user1' => $this->u_id_1, 'user2' =>$this->u_id_2],
            ['user1' => 'required', 'user2' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addFriend()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->t_id;
    }

    public function invite($u_id)
    {
        // always keep the smaller one in the front
        if ($this->u_id_1 > $this->u_id_2) {
            $tmp = $this->u_id_2;
            $this->u_id_2 = $this->u_id_1;
            $this->u_id_1 = $tmp;
            $this->t_inviter == 1 ? $this->t_inviter = 2 : $this-> t_inviter = 1;
        }
        $chk = UsersFriend::where('u_id_1', '=', $this->u_id_1)->where('u_id_2', '=', $this->u_id_2)->first();
        if (isset($chk->t_id)) {
            if ($chk->t_status == 1) {
                if ($chk->t_inviter == 1) {
                    $msg = $u_id == $chk->u_id_1 ? '您已经发出了邀请了, 请等待确认' : '对方已经邀请您了, 请及时确认';
                } else {
                    $msg = $u_id == $chk->u_id_2 ? '您已经发出了邀请了, 请等待确认' : '对方已经邀请您了, 请及时确认';
                }
            } else {
                $msg = '你们已经是好友了';
            }
            throw new Exception($msg, 3001);
        }
        $this->t_status = 1;
        return $this->addFriend();
    }

    public function confirm()
    {
        $now = new DateTime();
        $this->t_status = 2;
        $this->confirm_at = $now->format('Y-m-d H:i:s');
        return $this->save();
    }

    public static function findLinkById($id_1, $id_2)
    {
        if (!$id_1 || !$id_2) {
            throw new Exception("请传入有效的用户ID", 1);
        }
        if ($id_1 < $id_2) {
            $u_id_1 = $id_1;
            $u_id_2 = $id_2;
        } else {
            $u_id_1 = $id_2;
            $u_id_2 = $id_1;
        }
        $userFriend = UsersFriend::where('u_id_1', '=', $u_id_1)->where('u_id_2', '=', $u_id_2)->first();
        if (empty($userFriend->t_id)) {
            throw new Exception("请求的好友不存在", 30011);
        }
        return $userFriend;
    }

    public function remove()
    {
        return $this->delete();
    }

    public function user1()
    {
        return $this->belongsTo('User', 'u_id_1', 'u_id');
    }

    public function user2()
    {
        return $this->belongsTo('User', 'u_id_2', 'u_id');
    }
}
