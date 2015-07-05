<?php

/**
*
*/
class PostsPraise extends Eloquent
{

    public $primaryKey = 'pp_id';
    public $timestamps = false;

    public function addPraise()
    {
        $praise = PostsPraise::where('p_id', '=', $this->p_id)->where('u_id', '=', $this->u_id)->first();
        if (isset($praise)) {
            throw new Exception("已经点过赞了", 1);
        }
        $this->save();
        return true;
    }

    public function post()
    {
        return $this->belongsTo('Post', 'p_id', 'p_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function showInList()
    {
        return ['user' => $this->user->showInList(), 'praise_time' => $this->created_at->format('Y-m-d H:i:s')];
    }
}
