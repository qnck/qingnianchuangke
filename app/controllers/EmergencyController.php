<?php
/**
* 
*/
class EmergencyController extends \BaseController
{
    public function getFuck()
    {
        $query = User::select('users.*')->join('carts', function ($q) {
            $q->on('users.u_id', '=', 'carts.u_id')->where('carts.c_type', '=', 2);
        })->join('crowd_funding_products', function ($q) {
            $q->on('crowd_funding_products.p_id', '=', 'carts.p_id');
        })->leftJoin('orders', function ($q) {
            $q->on('orders.o_id', '=', 'carts.o_id');
        });
        $list = $query->get();
        $u_ids = [];
        foreach ($list as $key => $user) {
            if (!in_array($user->u_id, $u_ids)) {
                $u_ids[] = $user->u_id;
            }
        }
        return Response::json($u_ids);
    }

    public function sendYou()
    {
        
    }
}
