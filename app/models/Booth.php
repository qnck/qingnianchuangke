<?php
/**
*
*/
class Booth extends Eloquent
{
    public $primaryKey = 'b_id';
    public $timestamps = false;

    public static $type = [1 => '便利店', 2 => '创的店', 3 => '创的店与便利店'];

    private function baseValidate()
    {
        $validator = Validator::make(
            ['site' => $this->s_id, 'type' => $this->b_type, 'user' => $this->u_id, 'title' => $this->b_title],
            ['site' => 'required', 'type' => 'required', 'user'=> 'required', 'title' => 'required']
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
        $data = [];
        $data['id'] = $this->b_id;
        $data['title'] = $this->b_title;
        $data['desc'] = $this->b_desc;
        $data['type'] = $this->b_type;
        $data['category'] = $this->b_product_category;
        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->b_id;
        $data['title'] = $this->b_title;
        $data['desc'] = $this->b_desc;
        $data['type'] = $this->b_type;
        $data['category'] = $this->b_product_category;
        $data['source'] = $this->b_product_source;
        $data['imgs'] = explode(',', $this->b_imgs);
        $data['fans'] = $this->b_fans_count;
        $data['status'] = $this->b_status;
        $data['lng'] = $this->longitude;
        $data['lat'] = $this->latitude;
        $data['cust_group'] = $this->b_customer_group;
        $data['promo_strategy'] = $this->b_promo_strategy;
        $data['fund'] = $this->b_with_fund;

        $user = null;
        if (!empty($this->user)) {
            $user = $this->user->showInList();
        }
        $data['user'] = $user;

        return $data;
    }

    public function addBooth()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->b_id;
    }

    public function register()
    {
        $this->b_status = 0;
        return $this->addBooth();
    }

    public static function clearByUser($u_id)
    {
        $record = Booth::where('u_id', '=', $u_id)->where('b_status', '=', 0)->first();
        $record->delete();
    }

    // laravel relations
    
    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function products()
    {
        return $this->hasMany('Product', 'b_id', 'b_id');
    }

    public function promo()
    {
        return $this->hasMany('PromotionInfo', 'b_id', 'b_id');
    }

    public function fund()
    {
        return $this->hasOne('Fund', 'b_id', 'b_id');
    }
}
