<?php
/**
*
*/
class CrowdFunding extends Eloquent
{
    public $primaryKey = 'cf_id';
    public $timestamps = false;

    private $_imgs = [];

    public static function getCrowdFundingCate()
    {
        return [
            1 => '娱乐活动',
            2 => '生活百事',
            3 => '创业募资',
            4 => '艺术创作',
            5 => '设计发明',
            6 => '科学研究',
            7 => '公益事业'
        ];
    }

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'school' => $this->s_id, 'city' => $this->c_id, 'booth' => $this->b_id],
            ['user' => 'required', 'school' => 'required', 'city' => 'required', 'booth' => 'required']
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
        $this->loadImg();
        $data = [];
        $data['id'] = $this->cf_id;
        $data['cover_img'] = Img::filterKey('cover_img', $this->_imgs);
        $data['title'] = $this->c_title;
        $data['status'] = $this->c_status;
        $data['active_at'] = $this->active_at;
        $data['time'] = $this->c_time;
        $data['target_amount'] = $this->c_target_amount;
        if ($this->product) {
            $data['p_id'] = $this->product->p_id;
            $data['price'] = $this->product->p_price;
            $data['amount'] = $this->product->p_sold_quantity * $this->product->p_price;
        }
        if ($this->user) {
            $data['user'] = $this->user->showInList();
        }
        if ($this->school) {
            $data['school'] = $this->school->showInList();
        }
        if ($this->city) {
            $data['city'] = $this->city->showInList();
        }
        return $data;
    }

    public function showDetail()
    {
        $this->loadImg();
        $data = [];
        $data['id'] = $this->cf_id;
        $data['cover_img'] = Img::filterKey('cover_img', $this->_imgs);
        $content = json_decode($this->c_content);
        $prod_imgs = Img::filterKey('prod_img_', $this->_imgs, true);
        $pic_text = [];
        foreach ((array)$prod_imgs as $key => $value) {
            $tmp = ['img' => $value, 'text' => $content[$key]];
            $pic_text[] = $tmp;
        }
        $data['content'] = $pic_text;
        $data['title'] = $this->c_title;
        $data['status'] = $this->c_status;
        $data['active_at'] = $this->active_at;
        $data['time'] = $this->c_time;
        $data['yield_time'] = $this->c_yield_time;
        $data['target_amount'] = $this->c_target_amount;
        $data['shipping'] = $this->c_shipping;
        $data['shipping_fee'] = $this->c_shipping_fee;
        if ($this->product) {
            $data['p_id'] = $this->product->p_id;
            $data['price'] = $this->product->p_price;
            $data['amount'] = $this->product->p_sold_quantity * $this->product->p_price;
        }
        if ($this->user) {
            $data['user'] = $this->user->showInList();
        }
        if ($this->school) {
            $data['school'] = $this->school->showInList();
        }
        if ($this->city) {
            $data['city'] = $this->city->showInList();
        }
        if ($this->replies) {
            $data['replies'] = Reply::makeTree($this->replies);
        }
        return $data;
    }

    public function loadImg()
    {
        $this->_imgs = Img::toArray($this->c_imgs);
    }

    public function addCrowdFunding()
    {
        $this->baseValidate();
        $now = Tools::getNow();
        $this->created_at = $now;
        $this->c_status = 1;
        return $this->save();
    }

    // relations
    public function city()
    {
        return $this->hasOne('DicCity', 'c_id', 'c_id');
    }

    public function school()
    {
        return $this->hasOne('DicSchool', 't_id', 's_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function product()
    {
        return $this->hasOne('CrowdFundingProduct', 'cf_id', 'cf_id');
    }

    public function products()
    {
        return $this->hasMany('CrowdFundingProduct', 'c_id', 'c_id');
    }

    public function replies()
    {
        return $this->morphToMany('Reply', 'repliable');
    }
}
