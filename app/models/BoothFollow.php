<?php
/**
*
*/
class BoothFollow extends Eloquent
{
    public $primaryKey = 'f_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['booth' => $this->b_id, 'user' => $this->u_id],
            ['booth' => 'required', 'user' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function showInList()
    {
        $data = null;
        if (!empty($this->booth)) {
            $data = $this->booth->showDetail();
            $data['b_id'] = $data['id'];
        }
        $data['id'] = $this->f_id;
        return $data;
    }

    public function addFollow()
    {
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');

        $this->save();
        return $this->f_id;
    }

    public function follow()
    {
        $this->baseValidate();
        $chk = BoothFollow::where('b_id', '=', $this->b_id)->where('u_id', '=', $this->u_id)->first();
        if (!empty($chk->f_id)) {
            throw new Exception("您已经关注过了", 7004);
        }
        $booth = Booth::find($this->b_id);
        $booth->b_fans_count += 1;
        $booth->save();
        return $this->addFollow();
    }

    public function unfollow()
    {
        $chk = BoothFollow::where('b_id', '=', $this->b_id)->where('u_id', '=', $this->u_id)->first();
        if (!empty($chk)) {
            $chk->delete();
        } else {
            throw new Exception("已取消关注", 7004);
        }
        $booth = Booth::find($this->b_id);
        $booth->b_fans_count -= 1;
        if ($booth->b_fans_count <= 0) {
            $booth->b_fans_count = 0;
        }
        $booth->save();
        return true;
    }

    // laravel relation

    public function booth()
    {
        return $this->belongsTo('Booth', 'b_id', 'b_id');
    }

    public function follower()
    {
        return $this->hasOne('user', 'u_id', 'u_id');
    }
}
