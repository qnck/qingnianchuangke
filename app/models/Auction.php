<?php
/**
*
*/
class Auction extends Elquent
{
    public $primaryKey = 'a_id';
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['sub_title'] = $this->a_sub_title;
        $data['win_username'] = $this->a_win_username;
        $data['win_price'] = $this->a_win_price;
        $data['status'] = $this->a_status;
        if (empty($this->eventItme)) {
            $data['title'] = '';
            $data['start_at'] = '';
            $data['end_at'] = '';
            $data['url'] = '';
            $data['cover_img'] = null;
        }
        $data[''];
        return $data;
    }

    public function showDetail()
    {
    }

    // relation
    //
    public function winBid()
    {
        return $this->hasOne('AuctionBid', 'a_win_id', 'b_id');
    }

    public function eventItme()
    {
        return $this->hasOne('eventItme', 'e_id', 'e_id');
    }
}
