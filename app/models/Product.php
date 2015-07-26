<?php
/**
*
*/
class Product extends Eloquent
{
    public $primaryKey = 'p_id';
    public $timestamps = false;

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
