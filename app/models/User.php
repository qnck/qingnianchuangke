<?php

class User extends Eloquent
{

    public $primaryKey = 'u_id';

    /**
     * validate base info
     * @author Kydz 2015-06-14
     * @return bool
     */
    public function baseValidate()
    {
        $validator = Validator::make(
            ['mobile' => $this->u_mobile, 'pass' => $this->u_password],
            ['mobile' => 'required|digits:11', 'pass' => 'required|alpha_dash|min:6']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    /**
     * get unique token
     * @author Kydz 2015-06-14
     * @return string token
     */
    public function getUniqueToken()
    {
        $token = Str::random(32);
        if (User::where('u_token', '=', $token)->count() > 0) {
            $this->checkTokenUniqueness();
        }
        return $token;
    }

    /**
     * register user
     * @author Kydz 2015-06-14
     * @return string user token
     */
    public function register()
    {
        $this->baseValidate();
        // generate token
        $this->u_token = $this->getUniqueToken();
        // chcek if mobile exsits
        if (User::where('u_mobile', '=', $this->u_mobile)->count() > 0) {
            throw new Exception("手机号码已被使用", 1);
        }
        $this->u_password = Hash::make($this->u_password);
        $this->u_status = 1;
        $this->save();
        $re = [];
        $re['token'] = $this->u_token;
        $now = new Datetime();
        $now->modify('+ 30 days');
        $re['expire'] = $now->format('Y-m-d H:i:s');
        $re['id'] = $this->u_id;
        $re['name'] = $this->u_name;
        $re['head_img'] = $this->u_head_img;
        return $re;
    }

    /**
     * log user in
     * @author Kydz 2015-06-14
     * @return sting user token
     */
    public function login()
    {
        $this->baseValidate();
        $user = User::where('u_mobile', '=', $this->u_mobile)->first();
        if (!isset($user->u_id)) {
            throw new Exception("没有找到请求的用户", 1);
        }
        if (!Hash::check($this->u_password, $user->u_password)) {
            throw new Exception("密码错误", 1);
        } else {
            $re = [];
            $re['token'] = $user->u_token;
            $now = new Datetime();
            $now->modify('+ 30 days');
            $re['expire'] = $now->format('Y-m-d H:i:s');
            $re['id'] = $user->u_id;
            $re['name'] = $user->u_name;
            $re['head_img'] = $user->u_head_img;
            return $re;
        }
    }

    /**
     * update user info
     * @author Kydz 2015-06-14
     * @return bool
     */
    public function updateUser($imgToken = '')
    {
        $validator = Validator::make(
            [
                'nickname' => $this->u_nickname,
                'age' => $this->u_age,
                'name' => $this->u_name,
                'sex' => $this->u_sex,
                'school_name' => $this->u_school_name,
                'pass' => $this->u_password,
            ],
            [
                'nickname' => 'sometimes|max:32',
                'age' => 'sometimes|digits_between:1,3',
                'name' => 'sometimes|max:5',
                'sex' => 'sometimes|digits:1',
                'school_name' => 'sometimes',
                'pass' => 'sometimes',
            ]
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        }
        $user = User::chkUserByToken($this->u_token, $this->u_id);

        isset($this->u_nickname) ? $user->u_nickname = $this->u_nickname : '';
        isset($this->u_age) ? $user->u_age = $this->u_age : '';
        isset($this->u_name) ? $user->u_name = $this->u_name : '';
        isset($this->u_sex) ? $user->u_sex = $this->u_sex : '';
        isset($this->u_school_name) ? $user->u_school_name = $this->u_school_name : '';
        isset($this->u_password) ? $user->u_password = Hash::make($this->u_password) : '';
        isset($this->u_prof) ? $user->u_prof = $this->u_prof : '';
        isset($this->u_degree) ? $user->u_degree = $this->u_degree : '';
        isset($this->u_entry_year) ? $user->u_entry_year = $this->u_entry_year : '';

        if ($imgToken) {
            $img = new Img('user', $imgToken);
            $user->u_head_img = $img->getSavedImg($user->u_id, $user->u_head_img);
        }

        $user->updated_at = date('Y-m-d H:i:s');
        if (!$user->save()) {
            throw new Exception("更新用户信息失败", 1);
        } else {
            return true;
        }
    }

    /**
     * show list data
     * @author Kydz 2015-06-24
     * @return array data row
     */
    public function showInList()
    {
        $data = [];
        $data['id'] = $this->u_id;
        $data['name'] = $this->u_nickname;
        $data['head_img'] = $this->u_head_img;
        $data['school_name'] = $this->u_school_name;
        return $data;
    }

    /**
     * show detailed data
     * @author Kydz 2015-06-24
     * @return array detai info
     */
    public function showDetail()
    {
        if (!isset($this->u_id)) {
            throw new Exception("no such user", 1);
        }
        $data = [];
        $data['mobile'] = $this->u_mobile;
        $data['nickname'] = $this->u_nickname;
        $data['age'] = $this->u_age;
        $data['name'] = $this->u_name;
        $data['sex'] = $this->u_sex;
        $path = explode(',', $this->u_head_img);
        $path = array_pop($path);
        $data['head_img'] = $path;
        $data['identity_number'] = $this->u_identity_number;
        $path = explode(',', $this->u_identity_img);
        $path = array_pop($path);
        $data['identity_img'] = $path;
        $data['school_name'] = $this->u_school_name;
        $data['student_number'] = $this->u_student_number;
        $path = explode(',', $this->u_student_img);
        $path = array_pop($path);
        $data['student_img'] = $path;
        $data['address'] = $this->u_address;
        $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
        $data['follower_count'] = $this->u_follower_count;
        $data['following_count'] = $this->u_following_count;
        return $data;
    }

    /**
     * check if user is legal
     * @author Kydz 2015-06-24
     * @param  string $token user token
     * @return eloquent        user
     */
    public static function chkUserByToken($token, $id = 0)
    {
        if (empty($token)) {
            throw new Exception("please input token", 1);
        }
        $query = User::where('u_token', '=', $token)->where('u_status', '=', 1);
        if ($id > 0) {
            $query->where('u_id', '=', $id);
        }
        $user = $query->first();
        if (!isset($user->u_id)) {
            throw new Exception('您的登录已过期， 请重新登录', 1);
        } else {
            return $user;
        }
    }

    public function showBankCards()
    {
        $cards = [];
        if (isset($this->bankCards)) {
            foreach ($this->bankCards as $key => $card) {
                $cards[] = $card->showInList();
            }
        }
        return $cards;
    }

    public function showContact()
    {
        $data = [];
        if (isset($this->contact)) {
            $data = $this->contact->showDetail();
        }
        return $data;
    }

    public static function follow($from, $to)
    {
        $attention = new Attention();
        $attention->u_fans_id = $from->u_id;
        $attention->u_id = $to->u_id;
        $attention->created_at = date('Y-m-d H:i:s');
        if (!$attention->save()) {
            throw new Exception("fail to add follow relation", 1);
        }
        $from->u_following_count += 1;
        $to->u_follower_count += 1;
        if ($from->save() && $to->save()) {
            return true;
        } else {
            throw new Exception("fail to save", 1);
        }
    }

    public static function unfollow($from, $to)
    {
        $attention = Attention::where('u_fans_id', '=', $from->u_id)->where('u_id', '=', $to->u_id)->first();
        if (!isset($attention->a_id)) {
            throw new Exception("Error Processing Request", 1);
            
        }
        if (!$attention->delete()) {
            throw new Exception("fail to remove relation", 1);
        }
        $from->u_following_count -= 1;
        $to->u_follower_count -= 1;
        if ($from->save() && $to->save()) {
            return true;
        } else {
            throw new Exception("fail to save", 1);
        }
    }

    public function getPosts()
    {
        $posts = [];
        if (isset($this->posts)) {
            foreach ($this->posts as $key => $post) {
                $posts[] = $post->showInList();
            }
        }
        return $posts;
    }

    public function getFollowers()
    {
        $followers = [];
        if (isset($this->followers)) {
            foreach ($this->followers as $key => $follower) {
                $followers[] = $follower->showInList();
            }
        }
        return $followers;
    }

    public function getFollowings()
    {
        $followings = [];
        if (isset($this->followings)) {
            foreach ($this->followings as $key => $follower) {
                $followings[] = $follower->showInList();
            }
        }
        return $followings;
    }

    // eloquent realtions
    // 
    public function posts()
    {
        return $this->hasMany('Post', 'u_id', 'u_id');
    }

    public function replyPosts()
    {
        return $this->hasMany('PostsReply', 'u_id', 'to_u_id');
    }

    public function activity()
    {
        return $this->hasMany('Activity', 'u_id', 'ac_creat_user');
    }

    public function signedActivities()
    {
        return $this->hasMany('ActivitiesSignUser', 'u_id', 'u_id');
    }

    public function postPraises()
    {
        return $this->hasMany('PostsPraise', 'u_id', 'u_id');
    }

    public function followers()
    {
        return $this->belongsToMany('User', 'attentions', 'u_id', 'u_fans_id');
    }

    public function followings()
    {
        return $this->belongsToMany('User', 'attentions', 'u_fans_id', 'u_id');
    }

    public function bankCards()
    {
        return $this->hasMany('UsersBankCard', 'u_id', 'u_id');
    }

    public function contact()
    {
        return $this->hasOne('UsersContactPerson', 'u_id', 'u_id');
    }
}
