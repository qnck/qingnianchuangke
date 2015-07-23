<?php
/**
*
*/
class TmpUsersContactPeople extends Eloquent
{

    public $primaryKey = 'u_id';
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['teacher_name' => $this->u_teacher_name, 'teacher_phone' => $this->u_teacher_telephone, 'father_name' => $this->u_father_name, 'father_phone' => $this->u_father_telephone, 'mother_name' => $this->u_mother_name, 'mother_phone' => $this->u_mother_telephone, 'home_address' => $this->u_home_address],
            ['teacher_name' => 'required', 'teacher_phone' => 'required', 'father_name' => 'required', 'father_phone' => 'required', 'mother_name' => 'required', 'mother_phone' => 'required', 'home_address' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addContactPerson()
    {
        $this->baseValidate();
        $this->save();
        return $this->id;
    }

    public function register()
    {
        $this->u_status = 0;
        return $this->addContactPerson();
    }

    public function showDetail()
    {
        $data = [];
        $data['teacher_name'] = $this->u_teacher_name;
        $data['teacher_phone'] = $this->u_teacher_telephone;
        $data['father_name'] = $this->u_father_name;
        $data['father_telephone'] = $this->u_father_telephone;
        $data['mother_name'] = $this->u_mother_name;
        $data['mother_telephone'] = $this->u_mother_telephone;
        $data['frend_name1'] = $this->u_frend_name1;
        $data['frend_telephone1'] = $this->u_frend_telephone1;
        $data['frend_name2'] = $this->u_frend_name2;
        $data['frend_telephone2'] = $this->u_frend_telephone2;
        return $data;
    }

    public static function clearByUser($u_id)
    {
        $record = TmpUsersContactPeople::find($u_id);
        $record->delete();
    }

    // lavaral realtions

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}
