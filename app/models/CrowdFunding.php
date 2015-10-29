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
        $data['cover_img'] = Img::filterKey('cover_img', $this->_imgs);
        $data['title'] = $this->c_title;
        $data['status'] = $this->c_status;
        if ($this->product) {
            $data['price'] = $this->product->p_price;
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
}
