<?php
/**
*
*/
class Cart extends Eloquent
{
    public $primaryKey = 'c_id';
    public $timestamps = false;

    private $_quntityOri = 0;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['booth' => $this->b_id, 'user' => $this->u_id, 'product' => $this->p_id, 'price' => $this->c_price_origin, 'quntity' => $this->c_quantity, 'discount' => $this->c_discount],
            ['booth' => 'required', 'user' => 'required', 'product' => 'required', 'price' => 'required', 'quntity' => 'required', 'discount' => 'required']
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
        $data['id'] = $this->c_id;
        $data['prod_name'] = $this->p_name;
        $data['prod_img'] = $this->p_img;
        $data['price_origin'] = $this->c_price_origin;
        $data['price'] = $this->c_price;
        $data['discount'] = $this->c_discount;
        $data['quntity'] = $this->c_quantity;
        $data['status'] = $this->c_status;

        if (!empty($this->product)) {
            $data['product'] = $this->product->showInList();
        }
        return $data;
    }

    public function addCart()
    {
        $now = new DateTime();
        $this->loadProduct();
        $this->baseValidate();
        $this->c_price = $this->c_price_origin * $this->discount /100;
        $this->c_amount_origin = $this->c_price_origin * $this->c_quantity;
        $this->c_amount = $this->c_amount_origin * $this->c_discount /100;
        $this->c_status = 1;
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->c_id;
    }

    public function updateCart($quntityOri)
    {
        $this->_quntityOri = $quntityOri;
        $this->loadProduct();
        return $this->save();
    }

    public function removeCart()
    {
        $this->unloadProduct();
        return $this->delete();
    }

    public function loadProduct()
    {
        if ($this->c_quantity > 0 && $this->_quntityOri == 0) {
            throw new Exception("产品数量修改不匹配", 7003);
        }
        if (!$this->p_id || !$this->c_quantity) {
            throw new Exception("购买数量不能为0", 7001);
        }
        $product = Product::find($this->p_id);
        if (empty($product->p_id)) {
            throw new Exception("没有获取到产品", 7001);
        }

        // caculate quantity
        $product->load('quantity');
        if (empty($product->quantity->q_id)) {
            throw new Exception("没有获取到产品库存信息", 7001);
        }
        $total = $product->quantity->q_total;
        $sold = $product->quantity->q_sold;
        $inCart = Cart::where('c_status', '=', 1)->where('p_id', '=', $this->p_id)->sum('c_quantity');
        $remain = (int)$total - (int)$sold - (int)$inCart + $this->_quntityOri;
        if ($this->c_quantity > $remain) {
            throw new Exception("产品库存不足", 1);
        }
        $product->quantity->q_cart = $inCart + $this->c_quantity - $this->_quntityOri;
        $product->quantity->save();
        $this->c_price_origin = $product->p_price_origin;
        $this->c_discount = $product->p_discount;

        $this->p_name = $product->p_title;

        $imgs = Img::toArray($product->p_imgs);
        $this->p_img = array_pop($imgs);
    }

    public function unloadProduct()
    {
        if (!$this->p_id) {
            throw new Exception("该购物车没有绑定产品", 7001);
        }
        $product = Product::find($this->p_id);
        if (empty($product->p_id)) {
            throw new Exception("没有获取到产品", 7001);
        }

        // caculate quantity
        $product->load('quantity');
        if (empty($product->quantity->q_id)) {
            throw new Exception("没有获取到产品库存信息", 7001);
        }
        $product->quantity->q_cart -= $this->c_quantity;
        if ($product->quantity->q_cart < 0) {
            $product->quantity->q_cart = 0;
        }
        $product->quantity->save();
    }

    public static function bindOrder($order_ids)
    {
        
    }

    public function checkout()
    {

    }

    // laravel relation

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function product()
    {
        return $this->hasOne('product', 'p_id', 'p_id');
    }
}
