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
            $re = ['result' => 2001, 'data'=> [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
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
            $re = ['result' => 2001, 'data' => [], 'info' => $e->getMessage()];
        }
        return Response::json($re);
    }

    public function newBooth()
    {
        // base infos
        $vcode = Input::get('vcode', '');
        $mobile = Input::get('mobile', '');
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', '');
        $s_id = Input::get('s_id', '');
        // retrive data
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
        $laonSchema = Input::get('loan_schema');

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

        // img include id_img, student_img
        $imgToken = Input::get('img_token', '');

        try {
            $user = User::chkUserByToken($token, $u_id);

            $phone = new Phone($mobile);
            // $phone->authVCode($vcode);

            $user->u_school_id = $school;
            $user->u_prof = $profession;
            $user->u_degree = $degree;
            $user->u_entry_year = $entryYear;
            $user->update();

            $booth = new Booth();
            $booth->s_id = $s_id;
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

            if ($withFund == 0) {
                $b_id = $booth->regWithoutFund();
            } elseif ($withFund == 1) {
                $user_detail = new TmpUsersDetails();
                $user_detail->u_id = $u_id;
                $user_detail->u_identity_number = $idNum;
                $user_detail->u_student_number = $studentNum;

                $user_contact_people = new TmpUsersContactPeople();
                $user_contact_people->u_id = $u_id;
                $user_contact_people->u_teacher_name = $thName;
                $user_contact_people->u_teacher_telephone = $thPhone;
                $user_contact_people->u_father_name = $faName;
                $user_contact_people->u_father_telephone = $faPhone;
                $user_contact_people->u_mother_name = $moName;
                $user_contact_people->u_mother_telephone = $moPhone;
                $user_contact_people->u_frend_name1 = $frName1;
                $user_contact_people->u_frend_telephone1 = $frPhone1;
                $user_contact_people->u_frend_name2 = $frName2;
                $user_contact_people->u_frend_telephone2 = $frPhone2;
                $user_contact_people->u_home_address = $homeAddr;

                $user_card = new TmpUsersBankCard();
                $user_card->u_id = $u_id;
                $user_card->b_id = $bankId;
                $user_card->b_card_num = $cardNum;
                $user_card->b_holder_name = $cardHolderName;
                $user_card->b_holder_phone = $cardHolderPhone;
                $user_card->b_holder_identity = $cardHolderID;

                $b_id = $booth->regWithFund($user_detail, $user_contact_people, $user_card);

                $fund = new Fund();
                $fund->u_id = $u_id;
                $fund->t_apply_money = $loan;
                $fund->b_id = $b_id;
                $fund->t_profit_rate = $profitRate;
                $f_id = $fund->apply();

                $schema = 0;
                $allotedAmount = 0;
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
                    $repayment->apply();
                }

                if ($imgToken) {
                    $imgObj = new Img('user', $imgToken);
                    $imgs = $imgObj->getSavedImg($u_id, '', true);
                    $id_img = [];
                    $student_img = [];
                    foreach ($imgs as $k => $img) {
                        if ($k == 'identity_img_front' || $k == 'identity_img_back') {
                            $id_img[] = $img;
                        } elseif ($k == 'student_img_front' || $k == 'student_img_back') {
                            $student_img[] = $img;
                        }
                    }
                    $user_detail = TmpUsersDetails::find($u_id);
                    $user_detail->u_identity_img = implode(',', $id_img);
                    $user_detail->u_student_img = implode(',', $student_img);
                    $user_detail->save();
                }
            }
            $re = ['result' => 2000, 'data' => [], 'info' => '申请成功'];
        } catch (Exception $e) {
            // clean up todo
            TmpUsersDetails::clearByUser($u_id);
            TmpUsersBankCard::clearByUser($u_id);
            TmpUsersContactPeople::clearByUser($u_id);
            Booth::clearByUser($u_id);
            Fund::clearByUser($u_id);
            $re = ['result' => 2001, 'data' => [], 'info' => '申请失败:'.$e->getMessage()];
        }

        return Response::json($re);
    }

    public function boothList()
    {
        $u_id = Input::get('u_id', '');
        $token = Input::get('token');

        try {
            $user = User::chkUserByToken($token, $u_id);
            $data = Booth::where('u_id', '=', $u_id)->get();
            $list = [];
            foreach ($data as $key => $booth) {
                $tmp = $booth->showInList();
                $products_count = Product::where('b_id', '=', $booth->b_id)->where('p_status', '=', 1)->count();
                $tmp['prodct_count'] = $products_count;
                $list[] = $tmp;
            }
            $re = ['result' => 2000, 'data' => $list, 'info' => '获取我的所有店铺成功'];
        } catch (Exception $e) {
            $re = ['result' => 2001, 'data' => [], 'info' => '获取我的所有店铺失败:'.$e->getMessage()];
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
            $now = new DateTime();
            $now->modify('-8 hours');
            $boothInfo = $booth->showDetail();
            $products = Product::where('b_id', '=', $booth->b_id)->where('p_status', '=', 1)->where('p_active_at', '<', $now->format('Y-m-d H:i:s'))->with(['quantity'])->paginate(10);
            $list = [];
            foreach ($products as $key => $product) {
                $list[] = $product->showInList();
            }
            $pagination = ['total_record' => $products->getTotal(), 'total_page' => $products->getLastPage(), 'per_page' => $products->getPerPage(), 'current_page' => $products->getCurrentPage()];
            $data = ['booth' => $boothInfo, 'products' => $list, 'pagination' => $pagination];
            $re = ['result' => 2000, 'data' => $data, 'info' => '获取我的店铺成功'];
        } catch (Exception $e) {
            $re = ['result' => 7001, 'data' => [], 'info' => '获取我的店铺失败:'.$e->getMessage()];
        }
        return Response::json($re);
    }
}
