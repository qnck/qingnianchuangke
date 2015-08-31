<?php
/**
*
*/
class TmpUsersDetails extends Eloquent
{
    public $primaryKey = 'u_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['ID_number' => $this->u_identity_number, 'father_name' => $this->u_father_name, 'father_phone' => $this->u_father_telephone, 'mother_name' => $this->u_mother_name, 'mother_phone' => $this->u_mother_telephone, 'home_address' => $this->u_home_adress],
            ['ID_number' => 'required', 'father_name' => 'required', 'father_phone' => 'required', 'mother_name' => 'required', 'mother_phone' => 'required', 'home_address' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function chkByUser()
    {

    }

    public function showDetail()
    {
        $data = [];
        $data['identity_number'] = $this->u_identity_number;
        $data['identity_imgs'] = Img::toArray($this->u_identity_img);
        $data['home_adress'] = $this->u_home_adress;
        $data['father_name'] = $this->u_father_name;
        $data['father_telephone'] = $this->u_father_telephone;
        $data['mother_name'] = $this->u_mother_name;
        $data['mother_telephone'] = $this->u_mother_telephone;
        $data['status'] = $this->u_status;
        if ($this->u_status == 2) {
            $data['remark'] = $this->remark;
        }
        return $data;
    }

    public function addUserDetail()
    {
        $this->baseValidate();
        $this->save();
        return $this->id;
    }

    public function register()
    {
        $this->u_status = 0;
        return $this->addUserDetail();
    }

    // laravel relations
    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }

    public static function clearByUser($u_id)
    {
        $record = TmpUsersDetails::find($u_id);
        $record->delete();
    }

    public static function checkProfile($u_id)
    {
        $detail = TmpUsersDetails::find($u_id);
        if (!isset($detail->u_id)) {
            return 0;
        }
        if ($detail->u_status == 1) {
            return 1;
        } else {
            return 0;
        }
    }
}
