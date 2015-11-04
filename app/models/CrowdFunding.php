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
            2 => '个人生活',
            3 => '创业募集',
            4 => '艺术创作',
            5 => '创意发明',
            6 => '调查学习',
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
        $data['praise_count'] = $this->c_praise_count;
        $data['cate'] = $this->c_cate;
        $data['cate_label'] = $this->getCateLabel();
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
        $content = json_decode($this->c_content, JSON_OBJECT_AS_ARRAY);
        $prod_imgs = Img::filterKey('crowd_img_', $this->_imgs, true);
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
        $data['cate'] = $this->c_cate;
        $data['cate_label'] = $this->getCateLabel();
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

    public function getCateLabel()
    {
        if (!$this->c_cate) {
            return '';
        }
        $cates = CrowdFunding::getCrowdFundingCate();
        return $cates[$this->c_cate];
    }

    public function getParticipates()
    {
        $query = User::select('users.*', 'carts.c_quantity')->rightJoin('carts', function ($q) {
            $q->on('users.u_id', '=', 'carts.u_id')->where('carts.c_type', '=', 2);
        })->join('crowd_funding_products', function ($q) {
            $q->on('crowd_funding_products.p_id', '=', 'carts.p_id')->where('crowd_funding_products.cf_id', '=', $this->cf_id);
        });
        $list = $query->get();
        return $list;
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

    public function praises()
    {
        return $this->morphToMany('Praise', 'praisable');
    }

    public function favorites()
    {
        return $this->morphToMany('Favorite', 'favoriable');
    }
}
