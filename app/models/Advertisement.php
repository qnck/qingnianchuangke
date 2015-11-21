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
        if (empty($this->event)) {
            $this->load('event');
        }
        if (empty($this->event)) {
            $data['title'] = '';
            $data['cover_img'] = '';
            $data['url'] = '';
        }
        $data['title'] = $this->event->title;
        $data['cover_img'] = $this->event->cover_img;
        $data['url'] = $this->event->url;

        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->ad_id;
        $data['status'] = $this->ad_status;
        $data['o_id'] = $this->o_id;
        if (empty($this->event)) {
            $this->load('event');
        }
        if (empty($this->event)) {
            $data['title'] = '';
            $data['cover_img'] = '';
            $data['url'] = '';
            $data['range'] = '';
            $data['position'] = '';
            $data['start_at'] = '';
            $data['end_at'] = '';
        }
        $data['title'] = $this->event->title;
        $data['cover_img'] = $this->event->cover_img;
        $data['url'] = $this->event->url;
        $data['range'] = $this->event->e_range;
        $data['position'] = $this->event->e_position;
        $data['start_at'] = $this->event->e_start_at;
        $data['end_at'] = $this->event->e_end_at;
    }

    public function event()
    {
        return $this->hasOne('Event', 'e_id', 'e_id');
    }
}
