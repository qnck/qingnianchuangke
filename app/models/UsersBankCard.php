<?php
/**
*
*/
class UsersBankCard extends Eloquent
{

    public $primaryKey = 't_id';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function bank()
    {
        return $this->belongsTo('DicBank', 'b_id', 'b_id');
    }

    public function showInList()
    {
        $data = [];
        $data['card_number'] = $this->b_card_num;
        $data['bank'] = $this->bank->b_name;
        return $data;
    }
}
