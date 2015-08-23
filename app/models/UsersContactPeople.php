<?php
/**
*
*/
class UsersContactPeople extends Eloquent
{

    public $primaryKey = 'u_id';
    public $timestamps = false;

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

    // laravel relations

    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'u_id');
    }
}
