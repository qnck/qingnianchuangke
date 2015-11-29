<?php
/**
*
*/
class CrowdFunding extends Eloquent
{
    public $primaryKey = 'cf_id';
    public $timestamps = false;

    private $_imgs = [];

    public static function getStutus($key = null, $all = false)
    {
        $status = [
            1 => '审核中',
            2 => '审核未通过',
            3 => '众筹失败',
            4 => '众筹中',
            5 => '众筹成功'
        ];
        if ($key !== null && array_key_exists($key, $status)) {
            return $status[$key];
        } else {
            if ($all) {
                return $status;
            } else {
                return '未知状态';
            }
        }
    }

    public static function getCrowdFundingCate()
    {
        return [
            8 => '官方发布',
            1 => '娱乐活动',
            2 => '个人生活',
            3 => '创业募集',
            4 => '艺术创作',
            5 => '创意发明',
            6 => '调查学习',
            7 => '公益事业',
            9 => '下课约',
        ];
    }

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id],
            ['user' => 'required']
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
        if (empty($this->eventItem)) {
            $this->load(['eventItem']);
        }

        $data['cover_img'] = ['cover_img' => $this->eventItem->cover_img];
        $data['title'] = $this->eventItem->e_title;
        $data['brief'] = $this->eventItem->e_brief;
        $data['status'] = $this->c_status;
        $data['active_at'] = $this->eventItem->e_start_at;
        $date = new DateTime($this->created_at);
        $data['created_at'] = $date->format('Y-m-d');
        $data['time'] = $this->c_time;
        $data['time_left'] = $this->calculateTimeLeft();
        $data['target_amount'] = $this->c_target_amount;
        $data['praise_count'] = $this->c_praise_count;
        $data['mobile'] = $this->u_mobile;
        $data['cate'] = $this->c_cate;
        $data['current_time'] = Tools::getNow();
        $data['local_only'] = $this->c_local_only;
        $data['cate_label'] = $this->getCateLabel();
        $data['amount'] = $this->c_amount;
        if ($this->product) {
            $data['p_id'] = $this->product->p_id;
            $data['price'] = $this->product->p_price;
            $data['percentage'] = $this->product->getPercentage();
            $data['sold_quantity'] = $this->product->p_sold_quantity;
            $data['target_quantity'] = $this->product->p_target_quantity;
            if ($this->product->p_max_quantity == $this->product->p_target_quantity) {
                $data['is_limit'] = 1;
            } else {
                $data['is_limit'] = 0;
            }
        }
        if ($this->user) {
            $data['user'] = $this->user->showInList();
        }
        if ($this->school) {
            $data['school'] = $this->school->showInList();
        }
        if ($this->city) {
            if (empty($this->school)) {
                $this->load('school');
            }
            $data['city'] = $this->city()->where('c_province_id', '=', $this->school->t_province)->first()->showInList();
        }
        return $data;
    }

    public function showDetail()
    {
        $this->loadImg();
        $data = [];
        if (empty($this->eventItem)) {
            $this->load(['eventItem']);
        }
        $data['id'] = $this->cf_id;
        $data['cover_img'] = ['cover_img' => $this->eventItem->cover_img];
        $data['content'] = $this->getContent();
        $data['title'] = $this->eventItem->e_title;
        $data['brief'] = $this->eventItem->e_brief;
        $data['status'] = $this->c_status;
        $data['active_at'] = $this->eventItem->e_start_at;
        $date = new DateTime($this->created_at);
        $data['created_at'] = $date->format('Y-m-d');
        $data['time'] = $this->c_time;
        $data['time_left'] = $this->calculateTimeLeft();
        $data['yield_time'] = $this->c_yield_time;
        $data['target_amount'] = $this->c_target_amount;
        $data['shipping'] = $this->c_shipping;
        $data['shipping_fee'] = $this->c_shipping_fee;
        $data['mobile'] = $this->u_mobile;
        $data['cate'] = $this->c_cate;
        $data['cate_label'] = $this->getCateLabel();
        $data['yield_desc'] = $this->c_yield_desc;
        $data['local_only'] = $this->c_local_only;
        $data['current_time'] = Tools::getNow();
        $data['open_file'] = $this->c_open_file;
        $data['amount'] = $this->c_amount;
        if ($this->product) {
            $data['p_id'] = $this->product->p_id;
            $data['price'] = $this->product->p_price;
            $data['percentage'] = $this->product->getPercentage();
            $data['sold_quantity'] = $this->product->p_sold_quantity;
            $data['target_quantity'] = $this->product->p_target_quantity;
            if ($this->product->p_max_quantity == $this->product->p_target_quantity) {
                $data['is_limit'] = 1;
            } else {
                $data['is_limit'] = 0;
            }
        }
        if ($this->user) {
            $data['user'] = $this->user->showInList();
        }
        if ($this->school) {
            $data['school'] = $this->school->showInList();
        }
        if ($this->city) {
            if (empty($this->school)) {
                $this-load('school');
            }
            $data['city'] = $this->city()->where('t_province_id', '=', $this->school->t_province);
        }
        if ($this->replies) {
            $tmp = [];
            foreach ($this->replies as $key => $reply) {
                $tmp[] = $reply->showInList();
            }
            $data['replies'] = $tmp;
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

    public function getParticipates($per_page, $count = false)
    {
        $query = User::select('users.*', 'carts.c_quantity', 'orders.o_id', 'orders.o_comment', 'orders.o_shipping_address', 'orders.o_comment', 'orders.o_shipping_phone')->join('carts', function ($q) {
            $q->on('users.u_id', '=', 'carts.u_id')->where('carts.c_type', '=', 2);
        })->join('crowd_funding_products', function ($q) {
            $q->on('crowd_funding_products.p_id', '=', 'carts.p_id')->where('crowd_funding_products.cf_id', '=', $this->cf_id);
        })->leftJoin('orders', function ($q) {
            $q->on('orders.o_id', '=', 'carts.o_id');
        });
        if ($count) {
            return $query->count();
        }
        $list = $query->paginate($per_page);
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
        return $this->save();
    }

    public function addCensorLog($content)
    {
        $log = new LogUserProfileCensors();
        $log->u_id = $this->u_id;
        $log->cate = 'crowd_funding';
        $log->content = $content;
        $log->admin_id = Tools::getAdminId();
        $log->addLog();
    }

    public function calculateTimeLeft()
    {
        if (!$this->eventItem->e_start_at) {
            return -1;
        }
        $now = new DateTime();
        $active_at = new DateTime($this->eventItem->e_start_at);
        if ($active_at > $now) {
            $gap = -1;
        } else {
            $gap = $now->diff($active_at);
            $gap = $gap->days;
            $gap = $this->c_time - $gap;
            if ($gap < 0) {
                $gap = 0;
            }
        }
        return $gap;
    }

    public function getContent()
    {
        $content = json_decode($this->c_content, JSON_OBJECT_AS_ARRAY);
        $pic_text = [];
        if (empty($this->_imgs)) {
            $this->loadImg();
        }
        $imgs = Img::filterKey('crowd_img_', $this->_imgs, true);
        foreach ($imgs as $key => $img) {
            $txt = empty($content[$key]) ? '' : $content[$key];
            $pic_text[] = ['img' => $img, 'text' => $txt];
        }

        return $pic_text;
    }

    public function censor()
    {
        $old_status = '审核之前的状态为: '.CrowdFunding::getStutus($this->getOriginal('c_status')).', 审核之后的状态为: '.CrowdFunding::getStutus($this->c_status).'.';
        if ($this->c_status == 2) {
            $content = '众筹审核未通过, '.$old_status.' 备注: '.$this->remark;
        } elseif ($this->c_status == 1) {
            $content = '众筹审核通过, '.$old_status;
        } else {
            $content = '审核众筹记录, '.$old_status;
        }
        $msg = new MessageDispatcher($this->u_id);
        $msg->fireTextToUser($content);
        $this->addCensorLog($content);
        return $this->save();
    }

    public function delCrowdFunding()
    {
        if ($this->c_status > 4) {
            throw new Exception("众筹状态已完成", 2001);
        }
        $this->load(['replies', 'praises', 'favorites']);
        // check replies
        if (count($this->replies) > 0) {
            throw new Exception("已关联评论信息", 2001);
        }
        // check praises
        if (count($this->praises) > 0) {
            throw new Exception("已关联点赞信息", 2001);
        }
        // check favorites
        if (count($this->favorites) > 0) {
            throw new Exception("已关联收藏信息", 2001);
        }

        $funding_product = CrowdFundingProduct::where('cf_id', '=', $this->cf_id)->first();
        if (empty($funding_product)) {
            throw new Exception("库存信息丢失", 2001);
        }
        if (!Cart::getCartTypeCount(Cart::$TYPE_CROWD_FUNDING, $funding_product->p_id)) {
            throw new Exception("已有人购买", 2001);
        }
        $this->delete();
        $funding_product->delete();
        return true;
    }

    // relations
    public function city()
    {
        return $this->hasMany('DicCity', 'c_id', 'c_id');
    }

    public function eventItem()
    {
        return $this->hasOne('EventItem', 'e_id', 'e_id');
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
