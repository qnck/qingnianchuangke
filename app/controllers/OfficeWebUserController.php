<?php
/**
*
*/
class OfficeWebUserController extends \BaseController
{
    public function listUserProfiles()
    {
        $per_page = Input::get('per_page', 10000000);

        try {
            $query = DB::table('users')->select('users.u_id AS id', 'users.u_mobile', 'users.u_name', 'users.u_status', 'users.u_remark', 'dic_schools.t_name', 'tmp_user_profile_bases.u_status AS base_status', 'tmp_user_profile_bankcards.b_status AS bank_status')
            ->leftJoin('tmp_user_profile_bases', function ($q) {
                $q->on('users.u_id', '=', 'tmp_user_profile_bases.u_id');
            })->leftJoin('tmp_user_profile_bankcards', function ($q) {
                $q->on('users.u_id', '=', 'tmp_user_profile_bankcards.u_id');
            })->leftJoin('dic_schools', function ($q) {
                $q->on('dic_schools.t_id', '=', 'users.u_school_id');
            });

            $list = $query->paginate($per_page);
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

    // abondan
    public function getUserProfile($id)
    {
        try {
            $user = User::find($id);
            $bank = TmpUserProfileBankcard::find($id);
            $base = TmpUserProfileBase::find($id);

            if (empty($user)) {
                throw new Exception("查找的用户的不存在", 10001);
            } else {
                $user_data = $user->showDetail();
            }

            if (empty($bank)) {
                $user_bank = [];
            } else {
                $user_bank = $bank->showDetail();
            }

            if (empty($base)) {
                $user_base = [];
            } else {
                $user_base = $base->showDetail();
            }

            $data = [];
            $data['user'] = $user_data;
            $data['bank'] = $user_bank;
            $data['base'] = $user_base;

            $re = Tools::reTrue('获取用户信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户信息成功:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function censorUserProfileBase($id)
    {
        $check = Input::get('check', 0);
        $remark = Input::get('remark', '');

        try {
            if ($check == 0) {
                if (!$remark) {
                    throw new Exception("备注不能为空", 10001);
                }
            }

            $tmp_base = TmpUserProfileBase::find($id);
            if (empty($tmp_base)) {
                throw new Exception("查找的用户信息不存在", 10001);
            }

            if ($tmp_base->u_status == 1) {
                throw new Exception("审核已经通过了", 10002);
            }

            $base = UserProfileBase::find($id);
            if (empty($base)) {
                $base = new UserProfileBase();
            }

            if ($check == 1) {
                $base->u_id = $tmp_base->u_id;
                $base->u_id_number = $tmp_base->u_id_number;
                $base->u_id_imgs = $tmp_base->u_id_imgs;
                $base->u_is_id_verified = $tmp_base->u_is_id_verified;
                $base->s_id = $tmp_base->s_id;
                $base->u_entry_year = $tmp_base->u_entry_year;
                $base->u_major = $tmp_base->u_major;
                $base->u_student_number = $tmp_base->u_student_number;
                $base->u_student_imgs = $tmp_base->u_student_imgs;
                $base->u_is_student_verified = $tmp_base->u_is_student_verified;
                $base->em_contact_phone = $tmp_base->em_contact_phone;
                $base->em_contact_name = $tmp_base->em_contact_name;
                $base->u_father_name = $tmp_base->u_father_name;
                $base->u_father_phone = $tmp_base->u_father_phone;
                $base->u_mother_name = $tmp_base->u_mother_name;
                $base->u_mother_phone = $tmp_base->u_mother_phone;
                $base->u_home_address = $tmp_base->u_home_address;
                $base->save();
                $tmp_base->u_status = 1;
                $tmp_base->remark = '';
            } else {
                $tmp_base->u_status = 2;
                $tmp_base->remark = $remark;
            }
            $tmp_base->censor();
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

            $tmp_bank = TmpUserProfileBankcard::find($id);
            if (empty($tmp_bank)) {
                throw new Exception("查找的用户信息不存在", 10001);
            }

            if ($tmp_bank->b_status == 1) {
                throw new Exception("审核已经通过了", 10002);
            }

            $bank = UserProfileBankcard::find($id);
            if (empty($bank)) {
                $bank = new UserProfileBankcard();
            }

            if ($check == 1) {
                $bank->u_id = $tmp_bank->u_id;
                $bank->b_id = $tmp_bank->b_id;
                $bank->b_card_number = $tmp_bank->b_card_number;
                $bank->b_holder_name = $tmp_bank->b_holder_name;
                $bank->b_holder_phone = $tmp_bank->b_holder_phone;
                $bank->b_holder_id_number = $tmp_bank->b_holder_id_number;
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

    public function enable($id)
    {
        $status = Input::get('status', 0);
        $remark = Input::get('remark', '');

        try {
            $user = User::find($id);
            if (empty($user)) {
                throw new Exception("没有找到请求的用户", 10001);
            }
            if ($status == 1) {
                $status = 1;
            } else {
                $status = -1;
            }
            $user->u_status = $status;
            $user->u_remark = $remark;
            $user->save();
            $re = Tools::reTrue('用户状态修改成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '用户状态修改失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
