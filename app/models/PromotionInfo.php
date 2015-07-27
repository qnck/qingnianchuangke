<?php
/**
*
*/
class PromotionInfo extends Eloquent
{
    public $primaryKey = 'p_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['content' => $this->p_content, 'range' => $this->p_range, 'city' => $this->c_id, 'school' => $this->s_id, 'booth' => $this->b_id],
            ['content' => 'required', 'range' => 'required', 'city' => 'required', 'school' => 'required', 'booth' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addPromo()
    {
        $this->baseValidate();
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->p_praise_count = 0;
        $this->p_reply_count = 0;
        $this->save();
        return $this->p_id;
    }

    public function showInList()
    {
        $data = [];
        $data['content'] = $this->p_content;
        $data['praise_count'] = $this->p_praise_count;
        return $data;
    }
}
