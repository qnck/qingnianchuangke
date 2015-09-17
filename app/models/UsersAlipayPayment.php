<?php
/**
*
*/
class UsersAlipayPayment extends Eloquent
{
    public $primaryKey = 'u_id';
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['account'] = $this->t_account;
        return $data;
    }
}
