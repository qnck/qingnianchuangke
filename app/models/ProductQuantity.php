<?php
/**
*
*/
class ProductQuantity extends Eloquent
{
    public $primaryKey = 'q_id';
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->q_id;
        $data['sold'] = $this->q_sold;
        $data['remain'] = $this->q_remain;

        return $data;
    }

    // laravel relation
    
    public function product()
    {
        return $this->belongsTo('Product', 'p_id', 'p_id');
    }
}
