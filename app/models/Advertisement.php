<?php
/**
*
*/
class Advertisement extends Eloquent
{
    public $primaryKey = 'ad_id';
    public $timestamps = false;

    public function addAd()
    {
        $this->created_at = Tools::getNow();
        $this->ad_status = 1;
        return $this->save();
    }

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->ad_id;
        $data['status'] = $this->ad_status;
        $date = new DateTime($this->created_at);
        $data['created_at'] = $date->format('Y-m-d');

        if (empty($this->eventItem)) {
            $this->load('eventItem');
        }
        if (empty($this->eventItem)) {
            $data['title'] = '';
            $data['cover_img'] = null;
            $data['url'] = '';
            $data['brief'] = '';
            $data['active_at'] = '';
        } else {
            $data['title'] = $this->eventItem->e_title;
            $data['cover_img'] = Img::filterKey('cover_img', Img::toArray($this->eventItem->cover_img));
            $data['url'] = $this->eventItem->url;
            $data['brief'] = $this->eventItem->e_brief;
            $data['active_at'] = $this->eventItem->e_start_at;
        }

        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->ad_id;
        $data['status'] = $this->ad_status;
        $date = new DateTime($this->created_at);
        $data['created_at'] = $date->format('Y-m-d');
        $data['o_id'] = $this->o_id;
        if (empty($this->eventItem)) {
            $this->load('eventItem');
        }
        if (empty($this->eventItem)) {
            $data['title'] = '';
            $data['cover_img'] = '';
            $data['url'] = '';
            $data['range'] = '';
            $data['position'] = '';
            $data['start_at'] = '';
            $data['end_at'] = '';
        } else {
            $data = array_merge($data, $this->eventItem->showDetail());
        }
        return $data;
    }

    public function delAd()
    {
        $this->load(['eventItem']);
        if (!empty($this->eventItem)) {
            $this->eventItem->delEventItem();
        }
        $this->delete();
    }

    public static function fetchAd($position, $s_id = 0, $c_id = 0, $p_id = 0, $range = 1)
    {
        $now = Tools::getNow();
        $query = Advertisement::select('advertisements.*')
        ->with(['eventItem'])
        ->join('event_positions', function ($q) use ($position) {
            $q->on('event_positions.e_id', '=', 'advertisements.e_id')
            ->where('event_positions.position', '=', $position);
        })->join('event_ranges', function ($q) use ($s_id, $c_id, $p_id) {
            $q->on('event_ranges.e_id', '=', 'advertisements.e_id');
        })->where(function ($q) {
            $q->where('event_ranges.s_id', '=', 0)
            ->where('event_ranges.c_id', '=', 0)
            ->where('event_ranges.p_id', '=', 0);
        });
        if ($range == 2) {
            $query = $query->orWhere(function ($q) use ($s_id) {
                $q->where('event_ranges.s_id', '=', $s_id);
            });
        }
        if ($range == 3) {
            $query = $query->orWhere(function ($q) use ($c_id, $p_id) {
                $q->where('event_ranges.c_id', '=', $c_id)
                ->where('event_ranges.p_id', '=', $p_id);
            });
        }
        $query = $query->join('event_items', function ($q) {
            $q->on('event_items.e_id', '=', 'advertisements.e_id');
        })->where('event_items.e_start_at', '<', $now)
        ->where('event_items.e_end_at', '>', $now);
        $ads = $query->paginate(3);
        if (count($ads) > 0) {
            $data = [];
            foreach ($ads as $key => $ad) {
                $tmp = $ad->showInList();
                $tmp['item_type'] = 2;
                $data[] = $tmp;
            }
        } else {
            $data = null;
        }
        return $data;
    }

    // relation
    //
    public function eventItem()
    {
        return $this->hasOne('EventItem', 'e_id', 'e_id');
    }
}
