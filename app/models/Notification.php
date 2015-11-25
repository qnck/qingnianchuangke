<?php
/**
*
*/
class Notification extends Eloquent
{
    public $primaryKey = 'n_id';
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->n_id;
        $data['icon'] = $this->n_icon;
        $data['title'] = $this->n_title;
        $data['brief'] = $this->n_brief;
        $data['url'] = $this->n_url;
        $data['type'] = $this->n_type;
        $time = new DateTime($this->created_at);
        $data['created_at'] = $time->format('Y-m-d H:i:s');
        if (empty($this->is_read)) {
            $data['is_read'] = 0;
        } else {
            $data['is_read'] = $this->is_read;
        }
        return $data;
    }

    public function showDetail()
    {
        $data = [];
        $data['id'] = $this->n_id;
        $data['icon'] = $this->n_icon;
        $data['title'] = $this->n_title;
        $data['brief'] = $this->n_brief;
        $data['content'] = $this->n_content;
        $data['url'] = $this->n_url;
        $data['type'] = $this->n_type;
        $time = new DateTime($this->created_at);
        $data['created_at'] = $time->format('Y-m-d H:i:s');
        return $data;
    }

    public function addNot()
    {
        $this->n_status = 1;
        $this->created_at = Tools::getNow();
        return $this->save();
    }
}
