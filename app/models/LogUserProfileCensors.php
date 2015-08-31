<?php
/**
*
*/
class LogUserProfileCensors extends Eloquent
{
    public $primaryKey = 'log_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'category' => $this->cate, 'admin' => $this->admin_id, 'content' => $this->content],
            ['user' => 'required', 'category' => 'required', 'admin' => 'required', 'content' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addLog()
    {
        $this->baseValidate();

        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');

        $this->save();
        return $this->log_id;
    }
}
