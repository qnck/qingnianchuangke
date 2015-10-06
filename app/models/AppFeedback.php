<?php
/**
*
*/
class AppFeedback extends Eloquent
{
    public $timestamps = false;

    public function addFeedback()
    {
        $now = new DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        return $this->save();
    }
}
