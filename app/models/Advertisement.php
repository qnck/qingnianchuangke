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
        if (empty($this->eventItem)) {
            $this->load('eventItem');
        }
        if (empty($this->eventItem)) {
            $data['title'] = '';
            $data['cover_img'] = '';
            $data['url'] = '';
        }
        $data['title'] = $this->eventItem->title;
        $data['cover_img'] = $this->eventItem->cover_img;
        $data['url'] = $this->eventItem->url;

        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->ad_id;
        $data['status'] = $this->ad_status;
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

    public function eventItem()
    {
        return $this->hasOne('EventItem', 'e_id', 'e_id');
    }
}
