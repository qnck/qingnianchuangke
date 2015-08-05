<?php
/**
*
*/
class ProductReply extends Eloquent
{
    public $primaryKey = 'r_id';
    public $timestamps = false;

    public function baseValidate()
    {
        $validator = Validator::make(
            ['content' => $this->r_content, 'user' => $this->u_id, 'status' => $this->r_status, 'product' => $this->p_id],
            ['content' => 'required', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'product' => 'required|digits_between:1,11']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    /**
     * add reply
     * @author Kydz 2015-06-17
     */
    public function addReply()
    {
        $now = new DateTime();
        $this->r_status = 1;
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        if (!$this->save()) {
            throw new Exception("回复添加失败", 1);
        } else {
            return true;
        }
    }

    /**
     * disable reply
     * @author Kydz 2015-06-17
     * @return n/a
     */
    public function disable()
    {
        $this->baseValidate();
        $this->r_status = 0;
        $this->save();
    }

    /**
     * format data used in list
     * @author Kydz 2015-06-17
     * @return array list row
     */
    public function showInList()
    {
        $data = [];
        $data['id'] = $this->r_id;
        $data['reply_at'] = $this->created_at->format('Y-m-d H:i:s');
        $data['content'] = $this->r_content;
        $data['from_id'] = $this->u_id;
        $data['from_name'] = $this->u_name;
        $data['to_id'] = $this->to_u_id;
        $data['to_name'] = $this->to_u_name;
        $user = null;
        if (!empty($this->user)) {
            $user = $this->user->showInList();
        }
        $data['user'] = $user;

        $children = null;
        if (!empty($this->children)) {
            $list = [];
            foreach ($this->children as $reply) {
                $list[] = $reply->showInList();
            }
            $children = $list;
        }
        $data['children'] = $children;

        return $data;
    }

    // eloquent relations
    //
    public function product()
    {
        return $this->belongsTo('Product', 'p_id', 'p_id');
    }

    public function parent()
    {
        return $this->belongsTo('ProductReply', 'r_id', 'to_r_id');
    }

    public function children()
    {
        return $this->hasMany('ProductReply', 'to_r_id', 'r_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function toUser()
    {
        return $this->belongsTo('User', 'to_u_id', 'u_id');
    }
}
