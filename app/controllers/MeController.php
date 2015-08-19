<?php
/**
*
*/
class MeController extends \BaseController
{
    /**
     * get my-info
     * @author Kydz 2015-06-26
     * @return array detailed my info
     */
    public function me()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        try {
            $user = User::chkUserByToken($token, $u_id);
            $user = User::with('bankCards.bank', 'contact', 'school')->find($user->u_id);
            $userInfo = $user->showDetail();
            $cards = $user->showBankCards();
            $contact = $user->showContact();
            $school = $user->showSchool();
            $data = ['user_info' => $userInfo, 'cards' => $cards, 'contact' => $contact, 'school' => $school];
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取用户成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }

    /**
     * get my posts
     * @author Kydz 2015-07-04
     * @return array posts info
     */
    public function myPosts()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $keyWord = Input::get('key');
        try {
            $user = User::chkUserByToken($token, $u_id);
            $user = User::with([
                'posts' => function ($q) use ($keyWord) {
                    $q->where('p_status', '=', 1);
                    if (!empty($keyWord)) {
                        $q->where('p_title', 'LIKE', '%'.$keyWord.'%');
                    }
                },
                'posts.replys' => function ($q) {
                    $q->where('r_status', '=', 1);
                },
                'posts.replys.user',
                'posts.replys.toUser',
                'posts.praises',
                ])->find($user->u_id);
            $posts = $user->getPosts();
            $re = ['result' => 2000, 'data' => $posts, 'info' => '获取用户帖子成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * get my followers
     * @author Kydz 2015-07-04
     * @return json followers list
     */
    public function myFollowers()
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = $this->getUserFollowers($user->u_id);
            $re = ['result' => 2000, 'data' => $data, 'info'=> '获取我的粉丝成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * get my followings
     * @author Kydz 2015-07-04
     * @return json followings list
     */
    public function myFollowings()
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = $this->getUserFollowings($user->u_id);
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取我关注的人成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * reset pass word
     * @author Kydz 2015-07-04
     * @return json n/a
     */
    public function resetPass()
    {
        $mobile = Input::get('mobile');
        $vcode = Input::get('vcode');
        $newPass = Input::get('pass');

        $user = User::where('u_mobile', '=', $mobile)->first();

        // chcek if mobile exsits
        if (!isset($user->u_id)) {
            return Response::json(['result' => 2001, 'data' => [], 'info' => '没有查找到与该手机号码绑定的用户']);
        }
        $phone = new Phone($mobile);
        try {
            if ($phone->authVCode($vcode)) {
                $user->u_password = $newPass;
                $user->updateUser();
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '重置密码成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }

    /**
     * replies from me
     * @author Kydz
     * @return json reply list
     */
    public function myReply()
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');
        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = PostsReply::with(['post', 'toUser'])->where('u_id', '=', $u_id)->where('r_status', '=', 1)->paginate(10);
            $list = [];
            foreach ($data as $key => $reply) {
                $list[] = $reply->showInList();
            }
            $re = ['result' => 2000, 'data' => $list, 'info' => '获取我的回复成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    /**
     * praised from me
     * @author Kydz
     * @return json praised list
     */
    public function myPraise()
    {
        $u_id = Input::get('u_id');
        $token = Input::get('token');
        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = PostsPraise::with(['post'])->where('u_id', '=', $u_id)->paginate(10);
            $list = [];
            foreach ($data as $key => $praise) {
                $list[] = $praise->showInList();
            }
            $re = ['result' => 2000, 'data' => $list, 'info' => '获取的赞成功'];
        } catch (Exception $e) {
            $code = 2001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    public function postBooth()
    {
        // base infos
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        // s_id 在 数据里面存为c_id 用来标识所在城市, 而数据库中的 s_id 实际意义为 学校id
        $s_id = Input::get('s_id', '');

        // booth type
        $boothType = Input::get('type');
        // product category
        $productCate = Input::get('prod_cate');
        // booth title
        $boothTitle = Input::get('title');
        // booth position
        $boothLng = Input::get('lng');
        $boothLat = Input::get('lat');
        // product source
        $productSource = Input::get('prod_source');
        // customer group
        $cusomerGroup = Input::get('cust_group');
        // promo strategy
        $promoStratege = Input::get('promo_strategy');
        // with fund
        $withFund = Input::get('fund', 0);

        // profit ratio
        $profitRate = Input::get('profit');
        // loan amount
        $loan = Input::get('loan');
        // how to drow loan
        $laonSchema = Input::get('loan_schema', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $chk = Booth::where('u_id', '=', $u_id)->where('b_type', '=', $boothType)->first();

            if (isset($chk->b_id)) {
                throw new Exception("您已经申请过该类店铺了, 请勿重复提交", 1);
            }

            $booth = new Booth();
            $booth->c_id = $s_id;
            $booth->s_id = $user->u_school_id;
            $booth->u_id = $u_id;
            $booth->b_title = $boothTitle;
            $booth->b_desc = '';
            $booth->latitude = $boothLat;
            $booth->longitude = $boothLng;
            $booth->b_product_source = $productSource;
            $booth->b_product_category = $productCate;
            $booth->b_customer_group = $cusomerGroup;
            $booth->b_promo_strategy = $promoStratege;
            $booth->b_with_fund = $withFund;
            $booth->b_type = $boothType;
            $b_id = $booth->register();
            
            if ($withFund == 1) {
                $fund = new Fund();
                $fund->u_id = $u_id;
                $fund->t_apply_money = $loan;
                $fund->b_id = $b_id;
                $fund->t_profit_rate = $profitRate;
                $f_id = $fund->apply();

                $schema = 0;
                $allotedAmount = 0;

                $laonSchema = json_decode($laonSchema, true);

                if (!is_array($laonSchema)) {
                    throw new Exception("请传入正确的提款计划", 1);
                }

                foreach ($laonSchema as $key => $percentage) {
                    $percentage = $percentage / 100;
                    $schema ++;
                    if ($schema == count($laonSchema)) {
                        $amount = $loan - $allotedAmount;
                    } else {
                        $amount = $loan * $percentage;
                        $allotedAmount += $amount;
                    }
                    $repayment = new Repayment();
                    $repayment->f_id = $f_id;
                    $repayment->f_re_money = $amount;
                    $repayment->f_schema = $schema;
                    $repayment->f_percentage = $percentage;
                    $repayment->apply();
                }
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '申请成功'];
        } catch (Exception $e) {
            // clean up todo
            Booth::clearByUser($u_id);
            $f_id = Fund::clearByUser($u_id);
            Repayment::clearByFund($f_id);
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => $e->getMessage()];
        }

        return Response::json($re);
    }

    public function listBooth()
    {
        // echo 123;exit;
        $u_id = Input::get('u_id', '');
        $token = Input::get('token');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = Booth::where('u_id', '=', $u_id)->get();
            $list = [];
            foreach ($data as $key => $booth) {
                $tmp = $booth->showDetail();
                $products_count = Product::where('b_id', '=', $booth->b_id)->count();
                $tmp['prodct_count'] = $products_count;
                $list[] = $tmp;
            }
            $re = ['result' => 2000, 'data' => $list, 'info' => '获取我的所有店铺成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取我的所有店铺失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }

    public function booth($id)
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $booth = Booth::find($id);
            if (empty($booth->b_id) || $booth->u_id != $u_id) {
                throw new Exception("无法获取到请求的店铺", 1);
            }
            $booth->load('fund');
            $fund_info = null;
            if (!empty($booth->fund)) {
                $booth->fund->load('loans');
                $fund_info = $booth->fund->showDetail();
            }
            $boothInfo = $booth->showDetail();
            $boothInfo['fund_info'] = $fund_info;
            $re = ['result' => 2000, 'data' => $boothInfo, 'info' => '获取我的店铺成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取我的店铺失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function putBoothDesc($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $desc = Input::get('desc', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $booth = Booth::find($id);
            if (empty($booth->b_id) || $booth->u_id != $u_id) {
                throw new Exception("无法获取到请求的店铺", 7001);
            }
            $booth->b_desc = $desc;
            $booth->save();
            $re = ['result' => 2000, 'data' => [], 'info' => '更新店铺描述成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '更新店铺描述失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function getBoothStatus($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $booth = Booth::find($id);
            if ($booth->u_id != $u_id) {
                throw new Exception("没有权限操作改店铺", 7001);
            }
            $data = [];
            $data['open'] = $booth->b_open;
            $data['open_from'] = $booth->b_open_from;
            $data['open_to'] = $booth->b_open_to;
            $data['open_on'] = explode(',', $booth->b_open_on);
            $data['logo'] = $booth->getLogo();
            $re = Tools::reTrue('获取店铺状态信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取店铺状态信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function putBoothStatus($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $open = Input::get('open', 1);
        $openFrom = Input::get('open_from', '');
        $openTo = Input::get('open_to', '');
        $openOn = Input::get('open_on');
        $logo = Input::get('logo', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $booth = Booth::find($id);
            if (empty($booth->b_id) || $booth->u_id != $u_id) {
                throw new Exception("无法获取到请求的店铺", 7001);
            }
            $imgs = Img::toArray($booth->b_imgs);
            $imgs['logo'] = 'logo.'.$logo;
            $booth->b_imgs = implode(',', $imgs);
            $booth->b_open = $open;
            $booth->b_open_from = $openFrom;
            $booth->b_open_to = $openTo;
            $booth->b_open_on = $openOn;
            $booth->save();
            $re = Tools::reTrue('保存店铺状态成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '保存店铺状态失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function profileCheck()
    {
        $u_id = Input::get('u_id', 0);
        $token = Input::get('token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $bank = TmpUsersBankCard::checkProfile($u_id);
            $contact = TmpUsersContactPeople::checkProfile($u_id);
            $detail = TmpUsersDetails::checkProfile($u_id);
            $re = ['result' => 2000, 'data' => ['detail' => $detail, 'contact' => $contact, 'bank' => $bank], 'info' => '获取用户资料验证信息成功'];
        } catch (Exception $e) {
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取用户资料验证信息失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function getDetail()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $detail = TmpUsersDetails::find($u_id);
            $data = [];
            $data['name'] = $user->u_name;
            if (!isset($detail->u_id)) {
                $data['id_num'] = '';
                $data['id_img'] = '';
                $data['home_addr'] = '';
                $data['mo_name'] = '';
                $data['mo_phone'] = '';
                $data['fa_name'] = '';
                $data['fa_phone'] = '';
            } else {
                $data['id_num'] = $detail->u_identity_number;
                $imgs = Img::toArray($detail->u_identity_img);
                $data['id_img'] = $imgs;
                $data['home_addr'] = $detail->u_home_adress;
                $data['mo_name'] = $detail->u_mother_name;
                $data['mo_phone'] = $detail->u_mother_telephone;
                $data['fa_name'] = $detail->u_father_name;
                $data['fa_phone'] = $detail->u_father_telephone;
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取用户详细成功'];
        } catch (Exception $e) {
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取用户详细失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function postDetail()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');

        $name = Input::get('name', '');

        $idNum = Input::get('id_num', '');
        // home address
        $homeAddr = Input::get('home_addr');
        // mother name
        $moName = Input::get('mo_name');
        // mother phone
        $moPhone = Input::get('mo_phone');
        // father name
        $faName = Input::get('fa_name');
        // father phone
        $faPhone = Input::get('fa_phone');

        $imgToken = Input::get('img_token');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $user_detail = TmpUsersDetails::find($u_id);
            if (!isset($user_detail->u_id)) {
                $user_detail = new TmpUsersDetails();
            }
            if ($user_detail->u_status == 1) {
                throw new Exception("您的审核已经通过", 3002);
            }

            $user->u_name = $name;
            $user->save();

            $user_detail->u_id = $u_id;
            $user_detail->u_identity_number = $idNum;
            $user_detail->u_home_adress = $homeAddr;
            $user_detail->u_father_name = $faName;
            $user_detail->u_father_telephone = $faPhone;
            $user_detail->u_mother_name = $moName;
            $user_detail->u_mother_telephone = $moPhone;
            $user_detail->register();

            if ($imgToken) {
                $imgObj = new Img('user', $imgToken);
                $imgs = $imgObj->getSavedImg($u_id, '', true);
                $id_img = [];
                foreach ($imgs as $k => $img) {
                    if ($k == 'identity_img_front' || $k == 'identity_img_back') {
                        $id_img[] = $img;
                    }
                }
                $user_detail->u_identity_img = implode(',', $id_img);
                $user_detail->save();
            }


            $re = ['result' => 2000, 'data' => [], 'info' => '提交详细信息审核成功'];
        } catch (Exception $e) {
            TmpUsersDetails::clearByUser($u_id);
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '提交详细信息审核失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function getContact()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $contact = TmpUsersContactPeople::find($u_id);
            $contact->load('school');
            $data = [];
            if (!isset($contact->u_id)) {
                $data['th_name'] = '';
                $data['th_phone'] = '';
                $data['fr_name_1'] = '';
                $data['fr_phone_1'] = '';
                $data['fr_name_2'] = '';
                $data['fr_phone_2'] = '';
                $data['stu_num'] = '';
                $data['stu_img'] = '';
                $data['school'] = '';
                $data['profession'] = '';
                $data['degree'] = '';
                $data['entry_year'] = '';
            } else {
                $data['th_name'] = $contact->u_teacher_name;
                $data['th_phone'] = $contact->u_teacher_telephone;
                $data['fr_name_1'] = $contact->u_frend_name1;
                $data['fr_phone_1'] = $contact->u_frend_telephone1;
                $data['fr_name_2'] = $contact->u_frend_name2;
                $data['fr_phone_2'] = $contact->u_frend_telephone2;
                $data['stu_num'] = $contact->u_student_number;
                $imgs = Img::toArray($contact->u_student_img);
                $data['stu_img'] = $imgs;
                $data['school'] = $contact->school->showInList();
                $data['profession'] = $contact->u_prof;
                $data['degree'] = $contact->u_degree;
                $data['entry_year'] = $contact->u_entry_year;
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取用户详细成功'];
        } catch (Exception $e) {
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取用户详细失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function postContact()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');

        // shcool id
        $school = Input::get('school');
        // shcool entry year
        $entryYear = Input::get('entry_year');
        // profession area
        $profession = Input::get('profession');
        // graduate degree
        $degree = Input::get('degree');

        // studen card number
        $studentNum = Input::get('stu_num');
        // teacher name
        $thName = Input::get('th_name');
        // teacher phone
        $thPhone = Input::get('th_phone');
        // friend name 1
        $frName1 = Input::get('fr_name_1');
        // friend phone 1
        $frPhone1 = Input::get('fr_phone_1');
        // friend name 2
        $frName2 = Input::get('fr_name_2');
        // friend phone 2
        $frPhone2 = Input::get('fr_phone_2');

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

    public function getCard()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        
        try {
            $user = User::chkUserByToken($token, $u_id);
            $card = TmpUsersBankCard::where('u_id', '=', $u_id)->first();
            $card->load('bank');
            if (!isset($card->u_id)) {
                $data['bank'] = null;
                $data['card_num'] = '';
                $data['card_holder'] = '';
                $data['holder_phone'] = '';
                $data['holder_ID'] = '';
            } else {
                $data['bank'] = $card->bank->showInList();
                $data['card_num'] = $card->b_card_num;
                $data['card_holder'] = $card->b_holder_name;
                $data['holder_phone'] = $card->u_frend_telephone1;
                $data['holder_ID'] = $card->b_holder_identity;
            }
            $re = Tools::reTrue('获取用户银行卡成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户银行卡失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postCard()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        $vcode = Input::get('vcode', '');
        $mobile = Input::get('mobile', '');

        // id bank
        $bankId = Input::get('bank', 0);
        // bank card number
        $cardNum = Input::get('card_num', '');
        // card holder name
        $cardHolderName = Input::get('card_holder', '');
        // card holder phone
        $cardHolderPhone = Input::get('holder_phone', '');
        // card holder identy
        $cardHolderID = Input::get('holder_ID', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $phone = new Phone($mobile);
            $phone->authVCode($vcode);

            $card = TmpUsersBankCard::where('u_id', '=', $u_id)->first();
            if (!isset($card->u_id)) {
                $card = new TmpUsersBankCard();
            }
            if ($card->u_status == 1) {
                throw new Exception("您的审核已经通过", 3002);
            }
            $card->u_id = $u_id;
            $card->b_id = $bankId;
            $card->b_card_num = $cardNum;
            $card->b_holder_name = $cardHolderName;
            $card->b_holder_phone = $cardHolderPhone;
            $card->b_holder_identity = $cardHolderID;
            $card->register();
            $re = ['result' => 2000, 'data' => [], 'info' => '提交银行卡信息成功'];
        } catch (Exception $e) {
            TmpUsersBankCard::clearByUser($u_id);
            $code = 3002;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '提交银行卡信息失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function getProduct($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            if ($product->p_status == 2) {
                throw new Exception("该商品已下架", 7002);
            }
            $product->load('quantity', 'promo');
            $data = $product->showDetail();
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取商品成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取商品失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function getProducts()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $b_id = Input::get('b_id');
        $per_page = Input::get('per_page', 30);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $products = Product::with(['quantity', 'promo'])->where('u_id', '=', $u_id)->where('b_id', '=', $b_id)->where('p_status', '=', 1)->orderBy('sort', 'DESC')->paginate($per_page);
            $pagination = ['total_record' => $products->getTotal(), 'total_page' => $products->getLastPage(), 'per_page' => $products->getPerPage(), 'current_page' => $products->getCurrentPage()];
            $data = [];
            foreach ($products as $key => $product) {
                $data[] = $product->showInList();
            }
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取商品成功', 'pagination' => $pagination];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '获取商品失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function postProduct()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $b_id = Input::get('b_id', '');
        
        $prodName = Input::get('prod_name', '');
        $prodDesc = Input::get('prod_desc', '');
        $prodBrief = Input::get('prod_brief', '');
        $prodCost = Input::get('prod_cost', 0);
        $prodPriceOri = Input::get('prod_price', 0);
        $prodDiscount = Input::get('prod_discount', 100);
        $prodStock = Input::get('prod_stock', 0);
        $publish = Input::get('publish', 1);

        $promoDesc = Input::get('promo', '');
        $promoRange = Input::get('promo_range', 0);

        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            if ($prodDiscount > 0) {
                $prodPrice = $prodPriceOri * $prodDiscount / 100;
            } else {
                $prodPrice = $prodPriceOri;
            }

            $product = new Product();
            $product->b_id = $b_id;
            $product->p_title = $prodName;
            $product->u_id = $u_id;
            $product->p_cost = $prodCost;
            $product->p_price_origin = $prodPriceOri;
            $product->p_price = $prodPrice;
            $product->p_discount = $prodDiscount;
            $product->p_desc = $prodDesc;
            $product->p_brief = $prodBrief;
            $product->p_status = $publish == 1 ? 1 : 2;
            $p_id = $product->addProduct();
            $quantity = new ProductQuantity();
            $quantity->p_id = $p_id;
            $quantity->b_id = $b_id;
            $quantity->u_id = $u_id;
            $quantity->q_total = $prodStock;

            $quantity->addQuantity();

            if ($promoDesc) {
                $user->load('school');
                $promo = new PromotionInfo();
                $promo->p_id = $p_id;
                $promo->p_content = $promoDesc;
                $promo->c_id = $user->school->t_city;
                $promo->s_id = $user->school->t_id;
                $promo->b_id = $b_id;
                $promo->p_status = 1;
                $promo->p_range = $promoRange;
                $promo->addPromo();
            }

            if ($imgToken) {
                $imgObj = new Img('product', $imgToken);
                $imgs = $imgObj->getSavedImg($p_id, '', true);
                $product->p_imgs = implode(',', $imgs);
                $product->save();
            }

            $re = ['result' => 2000, 'data' => [], 'info' => '添加产品成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '添加产品失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function updateProduct($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $prodName = Input::get('prod_name', '');
        $prodBrief = Input::get('prod_brief', '');
        $prodDesc = Input::get('prod_desc', '');
        $prodCost = Input::get('prod_cost', 0);
        $prodPriceOri = Input::get('prod_price', 0);
        $prodDiscount = Input::get('prod_discount', 0);
        $prodStock = Input::get('prod_stock', 0);
        $publish = Input::get('publish', 1);

        $promoDesc = Input::get('promo', '');
        $promoRange = Input::get('promo_range', 0);

        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $product = Product::find($id);

            if (!isset($product->p_id) || $product->u_id != $u_id) {
                throw new Exception("没有找到请求的产品", 1);
            }

            if ($prodDiscount > 0) {
                $prodPrice = $prodPriceOri * $prodDiscount / 100;
            } else {
                $prodPrice = $prodPriceOri;
            }

            $product->p_title = $prodName;
            $product->p_cost = $prodCost;
            $product->p_price_origin = $prodPriceOri;
            $product->p_price = $prodPrice;
            $product->p_discount = $prodDiscount;
            $product->p_desc = $prodDesc;
            $product->sort = 1;
            $product->p_brief = $prodBrief;
            $product->p_status = $publish == 1 ? 1 : 2;
            $product->saveProduct($prodStock);

            if ($promoDesc) {
                $user->load('school');

                $promo = PromotionInfo::find($id);
                if (!isset($promo->p_id)) {
                    $promo = new PromotionInfo();
                    $promo->p_id = $id;
                    $promo->p_content = $promoDesc;
                    $promo->c_id = $user->school->t_city;
                    $promo->s_id = $user->school->t_id;
                    $promo->b_id = $product->b_id;
                    $promo->p_range = $promoRange;
                    $promo->addPromo();
                }
                $promo->p_status = 1;
                $promo->save();
            }

            if ($imgToken) {
                $imgObj = new Img('product', $imgToken);
                $imgs = $imgObj->getSavedImg($id, '', true);
                $product->p_imgs = implode(',', $imgs);
                $product->save();
            }

            $re = ['result' => 2000, 'data' => [], 'info' => '更新产品成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '更新产品失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function updateProductSort()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $sort = Input::get('sort', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $sortArray = json_decode($sort, true);
            if (!is_array($sortArray)) {
                throw new Exception("请传入正确的排序数据", 1);
            }
            $re = Product::updateSort($sortArray);
            $re = ['result' => 2000, 'data' => [], 'info' => '更新排序成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '更新排序失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function updateProductDiscount()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $discount = Input::get('discount', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $discountArray = json_decode($discount, true);
            if (!is_array($discountArray)) {
                throw new Exception("请传入正确的排序数据", 1);
            }
            $re = Product::updateDiscount($discountArray);
            $re = ['result' => 2000, 'data' => [], 'info' => '更新折扣成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '更新折扣失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function productOn($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $on = Input::get('on', 1);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            if (!isset($product->p_id)) {
                throw new Exception("您请求的商品不存在", 1);
            }
            $product->p_status = $on == 1 ? 1 : 2;
            $product->save();
            $re = ['result' => 2000, 'data' => [], 'info' => '产品操作成功'];
        } catch (Exception $e) {
            $code = 7001;
            if ($e->getCode() > 2000) {
                $code = $e->getCode();
            }
            $re = ['result' => $code, 'data' => [], 'info' => '产品操作失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }

    public function countOrders()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $count_nonshipping = Order::where('u_id', '=', $u_id)->where('o_shipping_status', '=', 1)->count();
            $count_shipped = Order::where('u_id', '=', $u_id)->where('o_shipping_status', '=', 5)->count();
            $count_nonpay = Order::where('u_id', '=', $u_id)->where('o_status', '=', 1)->count();
            $count_paied = Order::where('u_id', '=', $u_id)->where('o_status', '=', 2)->count();
            $count_finished = Order::where('u_id', '=', $u_id)->where('o_shipping_status', '=', 10)->count();
            $count_nonfinished = $count_nonshipping + $count_shipped;
            $data = ['nonshipping' => $count_nonshipping, 'shipped' => $count_shipped, 'nonpay' => $count_nonpay, 'paied' => $count_paied, 'nonfinished' => $count_nonfinished, 'finished' => $count_finished];
            $re = Tools::reTrue('获取订单统计成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取订单统计失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listOrders()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $shipping_status = Input::get('shipping', 0);
        $order_status = Input::get('order', 0);
        $key_word = Input::get('key', '');
        $finish = Input::get('finish', 0);
        $from = Input::get('from', '');
        $to = Input::get('to', '');

        $per_page = Input::get('per_page', 30);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $query = Order::select('orders.*')->with(['carts'])->leftJoin('carts', function ($j) {
                $j->on('orders.o_id', '=', 'carts.o_id');
            });
            if ($key_word) {
                $query = $query->where(function ($q) use ($key_word) {
                    $q->where('carts.p_name', 'LIKE', '%'.$key_word.'%')->orWhere('orders.o_number', 'LIKE', '%'.$key_word.'%');
                });
            }
            if ($shipping_status) {
                $query = $query->where('orders.o_shipping_status', '=', $shipping_status);
            }
            if ($order_status) {
                $query = $query->where('orders.o_status', '=', $order_status);
            }
            if ($from) {
                $query = $query->where('orders.created_at', '>', $from);
            }
            if ($to) {
                $query = $query->where('orders.created_at', '<', $to);
            }
            if ($finish == 1) {
                $query = $query->where('orders.o_shipping_status', '<', 10);
            } elseif ($finish == 2) {
                $query = $query->where('orders.o_shipping_status', '=', 10);
            }
            $list = $query->groupBy('carts.o_id')->paginate($per_page);
            $data = [];
            foreach ($list as $key => $order) {
                $data[] = $order->showDetail();
            }
            $re = Tools::reTrue('获取订单成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取订单失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function deliverOrder()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $orders = Input::get('orders', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $orders = explode(',', $orders);
            if (empty($orders)) {
                throw new Exception("无效的订单数据", 7001);
            }
            Order::updateShippingStatus($orders, Order::$SHIPPING_STATUS_DELIVERING);
            $re = Tools::reTrue('发货成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '发货失败:'.$e->getMessage());
        }
        return $re;
    }

    public function confirmOrder()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $orders = Input::get('orders', '');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $orders = explode(',', $orders);
            if (empty($orders)) {
                throw new Exception("无效的订单数据", 7001);
            }
            Order::updateShippingStatus($orders, Order::$SHIPPING_STATUS_FINISHED);
            $re = Tools::reTrue('发货成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '发货失败:'.$e->getMessage());
        }
        return $re;
    }

    public function listPraisePromo()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $list = DB::table('promotion_praises')->where('u_id', '=', $u_id)->lists('prom_id');
            $re = Tools::reTrue('获取我赞的产品成功', $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取我赞的产品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listFollowingBooth()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $list = DB::table('booth_follows')->where('u_id', '=', $u_id)->lists('b_id');
            $re = Tools::reTrue('获取我收藏的店铺成功', $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取我收藏的店铺失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getUserBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $data = [];
            $user = User::chkUserByToken($token, $u_id);
            $user->load('school');
            $user_contact = UsersContactPeople::find($u_id);
            if (empty($user_contact->u_id)) {
                $entry_year = '';
                $stu_imgs = '';
            } else {
                $entry_year = $user_contact->u_entry_year;
                $stu_imgs = Img::toArray($user_contact->u_student_img);
            }
            if (empty($stu_imgs)) {
                $stu_imgs = null;
            }
            $user_detail = UsersDetail::find($u_id);
            if (empty($user_detail->u_id)) {
                $id_imgs = '';
            } else {
                $id_imgs = Img::toArray($user_detail->u_identity_img);
            }
            if (empty($id_imgs)) {
                $id_imgs = null;
            }

            $data['id'] = $user->u_id;
            $data['name'] = $user->u_name;
            $data['home_imgs'] = Img::toArray($user->u_home_img);
            $data['stu_imgs'] = $stu_imgs;
            $data['id_imgs'] = $id_imgs;
            $data['entry_year'] = $entry_year;
            $data['gender'] = $user->u_sex;
            $data['biograph'] = $user->u_biograph;
            $data['school'] = $user->school->showInList();
            $brith_date = new DateTime($user->u_birthday);
            $data['birth'] = $brith_date->format('Y-m-d');
            $date['interests'] = $user->u_interests;
            $re = Tools::reTrue('获取用户基本信息成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取用户基本信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function putUserBase()
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        $name = Input::get('name', '');
        $birth = Input::get('birth', '');
        $gender = Input::get('gender', 0);
        $biograph = Input::get('biograph', '');
        $entry_year = Input::get('entry_year', '');
        $interests = Input::get('interests', '');

        $img_token = Input::get('img_token');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $user_contact = UsersContactPeople::find($u_id);
            if (empty($user_contact->u_id)) {
                $user_contact = new UsersContactPeople();
                $user_contact->u_id = $u_id;
            }
            $user_detail = UsersDetail::find($u_id);
            if (empty($user_detail->u_id)) {
                $user_detail = new UsersDetail();
                $user_detail->u_id = $u_id;
            }
            $user_contact->u_entry_year = $entry_year;

            $birth_date = new DateTime($birth);
            $user->u_name = $name;
            $user->u_birthday = $birth_date;
            $user->u_sex = $gender;
            $user->u_biograph = $biograph;
            $user->u_interests = $interests;
            if ($img_token) {
                $imgObj = new Img('user', $img_token);
                $imgs = $imgObj->getSavedImg($u_id, '', true);
                $home_imgs = Img::filterKey('home_img_', $imgs);
                $stu_imgs = Img::filterKey('student_img_', $imgs);
                $id_imgs = Img::filterKey('identity_img_', $imgs);
                $user->u_home_img = implode(',', $home_imgs);
                $user_contact->u_student_img = implode(',', $stu_imgs);
                $user_detail->u_identity_img = implode(',', $id_imgs);
            }
            $user_contact->save();
            $user_detail->save();
            $user->save();
            $re = Tools::reTrue('编辑基本信息成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '编辑信息失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
