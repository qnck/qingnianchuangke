<?php
/**
*
*/
class ProductQuantity extends Eloquent
{
    public $primaryKey = 'q_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['product' => $this->p_id, 'booth' => $this->b_id, 'user' => $this->u_id, 'stock' => $this->q_total],
            ['product' => 'required', 'booth' => 'required', 'user' => 'required', 'stock' => 'required']
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
        $data['id'] = $this->q_id;
        $data['sold'] = (int)$this->q_sold + (int)$this->q_cart;
        $data['total'] = (int)$this->q_total;

        return $data;
    }

    public function addQuantity()
    {
        $this->baseValidate();
        $this->save();
        return $this->q_id;
    }

    // laravel relation
    
    public function product()
    {
        return $this->belongsTo('Product', 'p_id', 'p_id');
    }
}
