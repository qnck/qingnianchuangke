<?php
/**
*
*/
class Product extends Eloquent
{
    public $primaryKey = 'p_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['booth' => $this->b_id, 'title' => $this->p_title, 'user' => $this->u_id, 'cost' => $this->p_cost, 'price' => $this->p_price, 'desc' => $this->p_desc],
            ['booth' => 'required', 'title' => 'required', 'user' => 'required', 'cost' => 'required', 'price' => 'required', 'desc' => 'required']
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
        $data['id'] = $this->p_id;
        $data['title'] = $this->p_title;
        $data['desc'] = $this->p_desc;
        $data['brief'] = $this->p_brief;
        $data['imgs'] = Img::toArray($this->p_imgs);
        $data['imgs'] = Img::filterKey('prod_img_', $data['imgs']);
        $data['price_origin'] = $this->p_price_origin;
        $data['price'] = $this->p_price;
        $data['discount'] = $this->p_discount;
        $data['sort'] = $this->sort;
        $data['reply_count'] = $this->p_reply_count;
        $data['status'] = $this->p_status;
        $data['remark'] = $this->p_remark;


        if (!empty($this->quantity)) {
            $quantity = $this->quantity->showInList();
            $data['quantity'] = $quantity;
        }
        if (!empty($this->promo)) {
            $promo = $this->promo->showInList();
            $data['promo'] = $promo;
        }

        return $data;
    }

    public function showDetail()
    {
        $data = null;
        $data['prod_name'] = $this->p_title;
        $data['prod_desc'] = $this->p_desc;
        $data['prod_brief'] = $this->p_brief;
        $data['prod_cost'] = $this->p_cost;
        $data['prod_price_origin'] = $this->p_price_origin;
        $data['prod_price'] = $this->p_price;
        $data['prod_discount'] = $this->p_discount;
        $data['imgs'] = Img::toArray($this->p_imgs);
        $data['imgs'] = Img::filterKey('prod_img_', $data['imgs']);
        $data['reply_count'] = $this->p_reply_count;
        $data['status'] = $this->p_status;
        $data['remark'] = $this->p_remark;

        $quantity = null;
        if (!empty($this->quantity)) {
            $quantity = $this->quantity->showInList();
        }
        $data['quantity'] = $quantity;

        $promo = null;
        if (!empty($this->promo)) {
            $promo = $this->promo->showDetail();
        }
        $data['promo'] = $promo;

        $replies = null;
        if (!empty($this->replies)) {
            $list = [];
            foreach ($this->replies as $key => $reply) {
                $list[] = $reply->showInList();
            }
            $replies = $list;
        }
        $data['replies'] = $replies;

        if (!empty($this->user)) {
            $data['user'] = $this->user->showDetail();
        }

        return $data;
    }

    public function addProduct()
    {
        // get max sort
        $sort = Product::where('u_id', '=', $this->u_id)->max('sort');
        if (empty($sort)) {
            $sort = 0;
        }
        $sort += 1;
        $now = new DateTime;
        $this->baseValidate();
        $this->sort = $sort;
        $this->p_reply_count = 0;
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->p_active_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->p_id;
    }

    public function saveProduct($stock)
    {
        $now = new DateTime;
        $this->baseValidate();
        $this->p_active_at = $now->format('Y-m-d H:i:s');
        $this->save();

        $quantity = ProductQuantity::where('p_id', '=', $this->p_id)->first();
        if (empty($quantity)) {
            $quantity = new ProductQuantity();
            $quantity->p_id = $this->p_id;
            $quantity->b_id = $this->b_id;
            $quantity->u_id = $this->u_id;
            $quantity->q_total = $stock;
            $quantity->addQuantity();
        }
        $freez = $quantity->q_cart + $quantity->q_sold;
        if ($freez > $stock) {
            throw new Exception("库存数量不能小于:".$freez, 1);
        }
        $quantity->q_total = $stock;
        $quantity->save();
        return $this->p_id;
    }

    public static function updateSort($sort)
    {
        $sql = 'UPDATE t_products SET sort = CASE p_id';
        $ids = [];
        foreach ($sort as $id => $s) {
            if (!is_numeric($id)) {
                throw new Exception("无效的排序数据", 1);
            }
            $sql .= ' WHEN '.$id.' THEN '.$s;
            $ids[] = $id;
        }
        $sql .= ' END WHERE p_id IN ('.implode(',', $ids).')';
        return DB::statement($sql);
    }

    public static function updateDiscount($discount)
    {
        $sql = 'UPDATE t_products SET ';
        $discountSql = ' p_discount = CASE p_id ';
        $priceSql = ' p_price = CASE p_id ';
        $ids = [];
        foreach ($discount as $id => $d) {
            if (!is_numeric($id)) {
                throw new Exception("无效的折扣数据", 7001);
            }
            if (!strpos($d, '@')) {
                throw new Exception("无效的折扣/百分比键值对", 7001);
            }
            $tmp = explode('@', $d);
            $discountSql .= ' WHEN '.$id.' THEN '.$tmp[0];
            $priceSql .= ' WHEN '.$id.' THEN '.$tmp[1];
            $ids[] = $id;
        }
        $sql = $sql.$discountSql.' END, '.$priceSql;
        $sql .= ' END WHERE p_id IN ('.implode(',', $ids).')';
        return DB::statement($sql);
    }

    // laravel relation
    
    public function quantity()
    {
        return $this->hasOne('ProductQuantity', 'p_id', 'p_id');
    }

    public function booth()
    {
        return $this->belongsTo('Booth', 'b_id', 'b_id');
    }

    public function promo()
    {
        return $this->hasOne('PromotionInfo', 'p_id', 'p_id');
    }

    public function replies()
    {
        return $this->hasMany('ProductReply', 'p_id', 'p_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}
