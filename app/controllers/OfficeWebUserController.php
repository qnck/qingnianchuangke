<?php
/**
*
*/
class OfficeWebUserController extends \BaseController
{
    public function listUserProfiles()
    {
        try {
            $query = DB::table('users')->select('users.u_id AS id', 'users.u_mobile', 'users.u_name', 'dic_schools.t_name', 'tmp_users_details.u_status AS detail_status', 'tmp_users_contact_peoples.u_status AS contact_status', 'tmp_users_bank_cards.b_status AS bank_status')->leftJoin('tmp_users_contact_peoples', function ($q) {
                $q->on('users.u_id', '=', 'tmp_users_contact_peoples.u_id');
            })->leftJoin('tmp_users_details', function ($q) {
                $q->on('users.u_id', '=', 'tmp_users_details.u_id');
            })->leftJoin('tmp_users_bank_cards', function ($q) {
                $q->on('users.u_id', '=', 'tmp_users_bank_cards.u_id');
            })->leftJoin('dic_schools', function ($q) {
                $q->on('dic_schools.t_id', '=', 'users.u_school_id');
            });

            $list = $query->paginate(30);
            $array = $list->toArray();
            $data['rows'] = [];
            foreach ($array['data'] as $key => $userProfile) {
                $data['rows'][] = $userProfile;
            }
            $data['total'] = $list->getTotal();
            $re = Tools::reTrue('获取用户信息成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户信息失败:'.$e->getMessage());
        }
        return Response::Json($re);
    }

    public function getUserProfile($id)
    {
        try {
            $user = User::find($id);
            $detail = TmpUsersDetails::find($id);
            $bank = TmpUsersBankCard::where('u_id', '=', $id)->first();
            $contact = TmpUsersContactPeople::find($id);

            if (empty($user)) {
                throw new Exception("查找的用户的不存在", 10001);
            } else {
                $user_data = $user->showDetail();
            }

            if (empty($detail)) {
                $user_detail = [];
            } else {
                $user_detail = $detail->showDetail();
            }

            if (empty($bank)) {
                $user_bank = [];
            } else {
                $user_bank = $bank->showDetail();
            }

            if (empty($contact)) {
                $user_contact = [];
            } else {
                $user_contact = $contact->showDetail();
            }

            $data = [];
            $data['user'] = $user_data;
            $data['detail'] = $user_detail;
            $data['bank'] = $user_bank;
            $data['contact'] = $user_contact;

            $re = Tools::reTrue('获取用户信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户信息成功:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorUserProfileDetail($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');

        try {
            if ($check == 0) {
                if (!$remark) {
                    throw new Exception("备注不能为空", 10001);
                }
            }

            $tmp_detail = TmpUsersDetails::find($id);
            if (empty($tmp_detail)) {
                throw new Exception("查找的用户信息不存在", 10001);
            }

            if ($tmp_detail->u_status == 1) {
                throw new Exception("审核已经通过了", 10002);
            }

            $detail = UsersDetail::find($id);
            if (empty($detail)) {
                $detail = new UsersDetail();
            }
            $old_status = $tmp_detail->u_status;
            if ($check == 1) {
                $detail->u_id = $tmp_detail->u_id;
                $detail->u_identity_number = $tmp_detail->u_identity_number;
                $detail->u_identity_img = $tmp_detail->u_identity_img;
                $detail->u_home_adress = $tmp_detail->u_home_adress;
                $detail->u_father_name = $tmp_detail->u_father_name;
                $detail->u_father_telephone = $tmp_detail->u_father_telephone;
                $detail->u_mother_name = $tmp_detail->u_mother_name;
                $detail->u_mother_telephone = $tmp_detail->u_mother_telephone;
                $detail->u_status = 1;
                $detail->save();
                $tmp_detail->u_status = 1;
                $tmp_detail->remark = '';
            } else {
                $tmp_detail->u_status = 2;
                $tmp_detail->remark = $remark;
            }
            $tmp_detail->censor();
            $re = Tools::reTrue('审核用户基本信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核用户基本信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorUserProfileContact($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');

        try {
            if ($check == 0) {
                if (!$remark) {
                    throw new Exception("备注不能为空", 10001);
                }
            }

            $tmp_contact = TmpUsersContactPeople::find($id);
            if (empty($tmp_contact)) {
                throw new Exception("查找的用户信息不存在", 10001);
            }

            if ($tmp_contact->u_status == 1) {
                throw new Exception("审核已经通过了", 10002);
            }

            $contact = UsersContactPeople::find($id);
            if (empty($contact)) {
                $contact = new UsersContactPeople();
            }

            if ($check == 1) {
                $contact->u_id = $tmp_contact->u_id;
                $contact->u_teacher_name = $tmp_contact->u_teacher_name;
                $contact->u_teacher_telephone = $tmp_contact->u_teacher_telephone;
                $contact->u_frend_name1 = $tmp_contact->u_frend_name1;
                $contact->u_frend_telephone1 = $tmp_contact->u_frend_telephone1;
                $contact->u_frend_name2 = $tmp_contact->u_frend_name2;
                $contact->u_frend_telephone2 = $tmp_contact->u_frend_telephone2;
                $contact->u_student_img = $tmp_contact->u_student_img;
                $contact->u_student_number = $tmp_contact->u_student_number;
                $contact->u_school_id = $tmp_contact->u_school_id;
                $contact->u_prof = $tmp_contact->u_prof;
                $contact->u_degree = $tmp_contact->u_degree;
                $contact->u_entry_year = $tmp_contact->u_entry_year;
                $contact->save();
                $tmp_contact->u_status = 1;
                $tmp_contact->remark = '';
            } else {
                $tmp_contact->u_status = 2;
                $tmp_contact->remark = $remark;
            }
            $tmp_contact->censor();
            $re = Tools::reTrue('审核用户联系人信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核用户联系人信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorUserProfileBank($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');

        try {
            if ($check == 0) {
                if (!$remark) {
                    throw new Exception("备注不能为空", 10001);
                }
            }

            $tmp_bank = TmpUsersBankCard::find($id);
            if (empty($tmp_bank)) {
                throw new Exception("查找的用户信息不存在", 10001);
            }

            if ($tmp_bank->b_status == 1) {
                throw new Exception("审核已经通过了", 10002);
            }

            $bank = UsersBankCard::find($id);
            if (empty($bank)) {
                $bank = new UsersBankCard();
            }

            if ($check == 1) {
                $bank->t_id = $tmp_bank->t_id;
                $bank->u_id = $tmp_bank->u_id;
                $bank->b_id = $tmp_bank->b_id;
                $bank->b_card_num = $tmp_bank->b_card_num;
                $bank->b_holder_name = $tmp_bank->b_holder_name;
                $bank->b_holder_phone = $tmp_bank->b_holder_phone;
                $bank->b_holder_identity = $tmp_bank->b_holder_identity;
                $bank->save();
                $tmp_bank->b_status = 1;
                $tmp_bank->remark = '';
            } else {
                $tmp_bank->b_status = 2;
                $tmp_bank->remark = $remark;
            }
            $tmp_bank->censor();
            $re = Tools::reTrue('审核用户银行信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '审核用户银行信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
