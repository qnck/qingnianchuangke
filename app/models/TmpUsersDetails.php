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
            ['ID_number' => $this->u_identity_number, 'student_number' => $this->u_student_number],
            ['ID_number' => 'required', 'student_number' => 'required']
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
}
