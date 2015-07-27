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
            ['teacher_name' => $this->u_teacher_name, 'teacher_phone' => $this->u_teacher_telephone, 'student_number' => $this->u_student_number],
            ['teacher_name' => 'required', 'teacher_phone' => 'required', 'student_number' => 'required']
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

    public static function checkProfile($u_id)
    {
        $contact = TmpUsersContactPeople::find($u_id);
        if (!isset($contact->u_id)) {
            return 0;
        }
        if ($contact->u_status == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    // lavaral realtions

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}