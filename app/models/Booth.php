<?php
/**
*
*/
class Booth extends Eloquent
{
    public $primaryKey = 'b_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['site' => $this->s_id, 'type' => $this->b_type, 'user' => $this->u_id, 'title' => $this->b_title],
            ['site' => 'required', 'type' => 'required', 'user'=> 'required', 'title' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addBooth()
    {
        $now = new DateTime();
        $this->baseValidate();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->save();
        return $this->b_id;
    }

    public function register()
    {
        $this->b_status = 0;
        return $this->addBooth();
    }

    public function regWithFund(TmpUsersDetails $user_detail, TmpUsersContactPeople $user_contact_people, TmpUsersBankCard $user_card)
    {
        $user_detail->register();
        $user_contact_people->register();
        $user_card->register();
        return $this->register();
    }

    public function regWithoutFund()
    {
        return $this->register();
    }

    public static function clearByUser($u_id)
    {
        $record = Booth::where('u_id', '=', $u_id)->where('b_status', '=', 0)->first();
        $record->delete();
    }

    // laravel relations
    
    public function user()
    {
        return $this->bleongsTo('User', 'u_id', 'u_id');
    }
}
