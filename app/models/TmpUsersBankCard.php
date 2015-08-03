<?php
/**
*
*/
class TmpUsersBankCard extends Eloquent
{

    public $primaryKey = 't_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['user' => $this->u_id, 'bank' => $this->b_id, 'card_number' => $this->b_card_num],
            ['user' => 'required', 'bank' => 'required', 'card_number' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function showInList()
    {
        $data = [];
        $data['card_number'] = $this->b_card_num;
        $data['bank'] = $this->bank->b_name;
        return $data;
    }

    public function addBankCard()
    {
        $this->baseValidate();
        $this->save();
        return $this->id;
    }

    public function register()
    {
        $this->b_status = 0;
        return $this->addBankCard();
    }

    public static function checkProfile($u_id)
    {
        $card = TmpUsersBankCard::where('u_id', '=', $u_id)->first();
        if (!isset($card->t_id)) {
            return 0;
        }
        if ($card->b_status == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    // laravel relations

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public function bank()
    {
        return $this->hasOne('DicBank', 'b_id', 'b_id');
    }

    public static function clearByUser($u_id)
    {
        $record = TmpUsersBankCard::where('u_id', '=', $u_id)->where('b_status', '=', 0)->first();
        $record->delete();
    }
}
