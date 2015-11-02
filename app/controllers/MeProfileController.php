<?php
/**
*
*/
class MeProfileController extends \BaseController
{
    public function getUserBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $data = [];
            $user = User::chkUserByToken($token, $u_id);
            $user->load('school');
            $profile = UserProfileBase::find($u_id);
            if (empty($profile->u_id)) {
                $entry_year = '';
                $stu_imgs = null;
                $id_imgs = null;
            } else {
                $entry_year = $profile->u_entry_year;
                $stu_imgs = Img::toArray($profile->u_student_imgs);
                $id_imgs = Img::toArray($profile->u_id_imgs);
            }

            $data['id'] = $user->u_id;
            $data['name'] = $user->u_name;
            $data['nickname'] = $user->u_nickname;
            $data['biograph'] = $user->u_biograph;
            $data['gender'] = $user->u_sex;
            $data['home_imgs'] = Img::toArray($user->u_home_img);
            $data['head_img'] = $user->u_head_img;
            $data['stu_imgs'] = $stu_imgs;
            $data['id_imgs'] = $id_imgs;
            $data['entry_year'] = $entry_year;
            $data['major'] = $profile->u_major;
            $data['id_verified'] = $profile->u_is_id_verified;
            $data['stu_verified'] = $profile->u_is_student_verified;
            $data['school'] = $user->school->showInList();

            $data['id_number'] = $profile->u_id_number;
            $data['stu_number'] = $profile->u_student_number;
            $data['emergency_name'] = $profile->em_contact_name;
            $data['emergency_phone'] = $profile->em_contact_phone;
            $data['apartment_no'] = $profile->u_apartment_no;

            $card = UserProfileBankcard::find($u_id);
            if (empty($card)) {
                $data['bank'] = '';
                $data['card_holder_name'] = '';
                $data['card_number'] = '';
            } else {
                $card->load('bank');
                $data['bank'] = $card->bank->showInList();
                $data['card_holder_name'] = $card->b_holder_name;
                $data['card_number'] = $card->b_card_number;
            }

            $data['father_name'] = $profile->u_father_name;
            $data['father_phone'] = $profile->u_father_phone;
            $data['mother_name'] = $profile->u_mother_name;
            $data['mother_phone'] = $profile->u_mother_phone;
            
            $re = Tools::reTrue('获取用户基本信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户基本信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
    
    public function postUserBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $name = Input::get('name', '');
        $id_num = Input::get('id_number', '');
        $id_school = Input::get('id_school', 0);
        $entry_year = Input::get('entry_year', '');
        $major = Input::get('major', '');
        $stu_num = Input::get('stu_num', '');
        $em_name = Input::get('emergency_name', '');
        $em_phoen = Input::get('emergency_phone', '');
        $father_name = Input::get('father_name', '');
        $father_phone = Input::get('father_phone', '');
        $mother_name = Input::get('mother_name', '');
        $mother_phone = Input::get('mother_phone', '');
        $apartment_no = Input::get('apartment_no', '');

        $img_token = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $profile = TmpUserProfileBase::find($u_id);
            if (empty($profile)) {
                $profile = new TmpUserProfileBase();
                $profile->u_id = $u_id;
            }
            $user->u_name = $name;
            $profile->u_id_number = $id_num;
            $profile->s_id = $id_school;
            $profile->u_entry_year = $entry_year;
            $profile->u_major = $major;
            $profile->u_student_number = $stu_num;
            $profile->em_contact_phone = $em_phoen;
            $profile->em_contact_name = $em_name;
            $profile->u_father_name = $father_name;
            $profile->u_father_phone = $father_phone;
            $profile->u_mother_name = $mother_name;
            $profile->u_mother_phone = $mother_phone;
            $profile->u_apartment_no = $apartment_no;
            $profile->register();
            if ($img_token) {
                $imgObj = new Img('user', $img_token);
                $imgs = $imgObj->getSavedImg($u_id, implode(',', [$profile->u_id_imgs, $profile->u_student_imgs]), true);
                $stu_imgs = Img::filterKey('student_img_', $imgs);
                $id_imgs = Img::filterKey('identity_img_', $imgs);
                $profile->u_student_imgs = implode(',', $stu_imgs);
                $profile->u_id_imgs = implode(',', $id_imgs);
            }
            $user->save();
            $profile->save();
            $re = Tools::reTrue('提交信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '提交信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function putUserBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $name = Input::get('name');
        $nickname = Input::get('nickname', '');
        $gender = Input::get('gender', '');
        $bio = Input::get('bio', '');
        $id_school = Input::get('id_school', 0);
        $entry_year = Input::get('entry_year', '');
        $major = Input::get('major', '');
        $stu_num = Input::get('stu_num', '');

        $img_token = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $profile = TmpUserProfileBase::find($u_id);
            if (empty($profile)) {
                $profile = new TmpUserProfileBase();
                $profile->u_id = $u_id;
            }
            $user->u_name = $name;
            $user->u_nickname = $nickname;
            $user->u_sex = $gender;
            $user->u_biograph = $bio;

            $profile->s_id = $id_school;
            $profile->u_entry_year = $entry_year;
            $profile->u_major = $major;

            if ($img_token) {
                $imgObj = new Img('user', $img_token);
                $imgs = $imgObj->getSavedImg($u_id, implode(',', [$profile->u_id_imgs, $profile->u_student_imgs, $user->u_home_img]), true);
                $stu_imgs = Img::filterKey('student_img_', $imgs);
                $id_imgs = Img::filterKey('identity_img_', $imgs);
                $home_imgs = Img::filterKey('home_img_', $imgs);
                $profile->u_student_imgs = implode(',', $stu_imgs);
                $profile->u_id_imgs = implode(',', $id_imgs);
                $user->u_home_img = implode(',', $home_imgs);
            }
            $profile->save();
            $user->save();
            $re = Tools::reTrue('提交信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '提交信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getBank()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $card = TmpUserProfileBankcard::find($u_id);
            $card->load('bank');
            if (!isset($card->u_id)) {
                $data['bank'] = null;
                $data['card_num'] = '';
                $data['card_holder'] = '';
                $data['holder_phone'] = '';
                $data['holder_ID'] = '';
            } else {
                $data['bank'] = $card->bank->showInList();
                $data['card_num'] = $card->b_card_number;
                $data['card_holder'] = $card->b_holder_name;
                $data['holder_phone'] = $card->b_holder_phone;
                $data['holder_ID'] = $card->b_holder_id_number;
            }
            $re = Tools::reTrue('获取用户银行卡成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户银行卡失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postBank()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');

        // id bank
        $bankId = Input::get('bank', 0);
        // bank card number
        $cardNum = Input::get('card_num', '');
        // card holder name
        $cardHolderName = Input::get('holder_name', '');
        // card holder phone
        $cardHolderPhone = Input::get('holder_phone', '');
        // card holder identy
        $cardHolderID = Input::get('holder_id', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $card = TmpUserProfileBankcard::find($u_id);
            if (!isset($card->u_id)) {
                $card = new TmpUserProfileBankcard();
            }
            if ($card->u_status == 1) {
                throw new Exception("您的审核已经通过", 3002);
            }
            $card->u_id = $u_id;
            $card->b_id = $bankId;
            $card->b_card_number = $cardNum;
            $card->b_holder_name = $cardHolderName;
            $card->b_holder_phone = $cardHolderPhone;
            $card->b_holder_id_number = $cardHolderID;
            $card->register();
            $re = Tools::reTrue('提交银行卡信息成功');
        } catch (Exception $e) {
            // TmpUserProfileBankcard::clearByUser($u_id);
            $re = Tools::reFalse($e->getCode(), '提交银行卡信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
