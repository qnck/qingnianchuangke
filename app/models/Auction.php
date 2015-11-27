<?php
/**
*
*/
class Auction extends Eloquent
{
    public $primaryKey = 'a_id';
    public $timestamps = false;

    public function showInList()
    {
        
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->a_id;
        $data['sub_title'] = $this->a_sub_title;
        $data['win_username'] = $this->a_win_username;
        $data['win_price'] = $this->a_win_price;
        $data['status'] = $this->a_status;
        $data['cost'] = $this->a_cost;
        if (empty($this->eventItem)) {
            $data['title'] = '';
            $data['start_at'] = '';
            $data['end_at'] = '';
            $data['url'] = '';
            $data['cover_img'] = [];
        } else {
            $data['title'] = $this->eventItem->e_title;
            $data['start_at'] = $this->eventItem->e_start_at;
            $data['end_at'] = $this->eventItem->e_end_at;
            $data['url'] = $this->eventItem->url;
            $data['cover_img'] = Img::filterKey('cover_img', Img::toArray($this->eventItem->cover_img));
        }
        return $data;
    }

    public function addAuction()
    {
        $this->created_at = Tools::getNow();
        $this->a_status = 1;
        return $this->save();
    }

    public static function runTheWheel()
    {
        $auction = Auction::join('event_items', function ($q) {
            $q->on('event_items.e_id', '=', 'auctions.e_id');
        })->where('event_items.e_end_at', '>', $now)
        ->where('auctions.a_status', '=', 1)->first();
        if (empty($auction)) {
            return true;
        }

        $bid = AuctionBid::where('a_id', '=', $auction->a_id)->orderBy('b_price', 'DESC')->frist();
        if (empty($bid)) {
            throw new Exception("无人出价", 2001);
        }
        if ($bid->is_win) {
            throw new Exception("中奖信息已处理", 2001);
        }
        $user = User::find($bid->u_id);
        $auction->a_win_usernam = $user->u_nickname;
        $auction->a_win_id = $bid->b_id;
        $auction->a_win_price = $bid->b_price;
        $auction->a_status = 2;
        $auction->save();

        $bid->is_win = 1;
        $bid->is_pay = 0;
        $bid->save();
        return true;
    }

    // relation
    //
    public function winBid()
    {
        return $this->hasOne('AuctionBid', 'a_win_id', 'b_id');
    }

    public function eventItem()
    {
        return $this->hasOne('eventItem', 'e_id', 'e_id');
    }
}
