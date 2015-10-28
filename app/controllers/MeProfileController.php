<?php
/**
*
*/
class MeProfileController extends \BaseController
{
    public function postBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');

        // shcool id
        $school = Input::get('school');
        // shcool entry year
        $entryYear = Input::get('entry_year');
        // profession area
        $profession = Input::get('profession');

        // studen card number
        $studentNum = Input::get('stu_num');
        // emergency contact
        $frName2 = Input::get('em_contact_phone');

        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $user_contact_people = TmpUsersContactPeople::find($u_id);
            if (!isset($user_contact_people->u_id)) {
                $user_contact_people = new TmpUsersContactPeople();
            }
            if ($user_contact_people->u_status == 1) {
                throw new Exception("您的审核已经通过", 3002);
            }
            $user_contact_people->u_id = $u_id;
            $user_contact_people->u_teacher_name = $thName;
            $user_contact_people->u_teacher_telephone = $thPhone;
            $user_contact_people->u_frend_name1 = $frName1;
            $user_contact_people->u_frend_telephone1 = $frPhone1;
            $user_contact_people->u_frend_name2 = $frName2;
            $user_contact_people->u_frend_telephone2 = $frPhone2;
            $user_contact_people->u_student_number = $studentNum;
            $user_contact_people->u_school_id = $school;
            $user_contact_people->u_prof = $profession;
            $user_contact_people->u_degree = $degree;
            $user_contact_people->u_entry_year = $entryYear;
            $user_contact_people->register();

            if ($imgToken) {
                $imgObj = new Img('user', $imgToken);
                $imgs = $imgObj->getSavedImg($u_id, '', true);
                $student_img = [];
                foreach ($imgs as $k => $img) {
                    if ($k == 'student_img_front' || $k == 'student_img_back') {
                        $student_img[] = $img;
                    }
                }
                $user_contact_people->u_student_img = implode(',', $student_img);
                $user_contact_people->save();
            }

            $re = ['result' => 2000, 'data' => [], 'info' => '提交学校信息成功'];
        } catch (Exception $e) {
            TmpUsersContactPeople::clearByUser($u_id);
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '提交学校信息失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }
}
