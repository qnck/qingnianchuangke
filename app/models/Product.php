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
        $data = [];
        $data['id'] = $this->p_id;
        $data['title'] = $this->p_title;
        $data['desc'] = $this->p_desc;
        $data['imgs'] = $this->p_imgs;
        $data['price'] = $this->p_price;
        $data['discount'] = $this->p_discount;
        $data['sort'] = $this->sort;

        $quantity = [];
        if (!empty($this->quantity)) {
            $quantity = $this->quantity->showInList();
        }

        $data['quantity'] = $quantity;

        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['prod_name'] = $this->p_title;
        $data['prod_desc'] = $this->p_desc;
        $data['prod_cost'] = $this->p_cost;
        $data['prod_price'] = $this->p_price;
        $data['prod_discount'] = $this->p_discount;
        $data['imgs'] = explode(',', $this->p_imgs);
        $quantity = [];
        if (isset($this->quantity)) {
            $quantity = $this->quantity->showInList();
        }
        $data['quantity'] = $quantity;
        return $data;
    }

    public function addProduct()
    {
        $now = new DateTime;
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->p_status = 1;
        $this->p_active_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->p_id;
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
}
