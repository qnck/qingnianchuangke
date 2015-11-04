<?php
/**
*
*/
class UserImport extends Eloquent
{
    protected $fillable = ['u_id', 'u_ext_id', 'u_ext_token', 'u_head_img', 'u_nickname', 'u_gender'];

    protected function baseValidate()
    {
        $validator = Validator::make(
            ['ext_id' => $this->u_ext_id],
            ['ext_id' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 3005);
        } else {
            return true;
        }
    }

    public function register($ext_id, $ext_token = '', $nickname = '', $head_img = '', $gender = 3)
    {
        $user = new User();
        $user->u_nickname = $nickname;
        $user->u_sex = $gender;
        $user->u_name = '';
        $user->u_token = $user->getUniqueToken();
        $user->created_at = Tools::getNow();
        $user->updated_at = Tools::getNow();
        $user->u_status = 1;
        $user->save();
        $data = ['u_id' => $user->u_id, 'ext_id' => $ext_id, 'ext_token' => $ext_token];
        return $data;
    }

    public function login($u_id)
    {
        $user = User::find($u_id);
        if (empty($user)) {
            throw new Exception("没有找到用户", 3005);
        }
        $re = [];
        $re['token'] = $user->u_token;
        $now = new Datetime();
        $now->modify('+ 30 days');
        $re['expire'] = $now->format('Y-m-d H:i:s');
        $re['id'] = $user->u_id;
        $re['name'] = $user->u_name;
        $re['nickname'] = $user->u_nickname;
        $re['head_img'] = Img::toArray($user->u_head_img);
        $re['head_img'] = empty($re['head_img']) ? '' : reset($re['head_img']);
        $school = DicSchool::find($user->u_school_id);
        if (empty($school)) {
            $re['site'] = null;
            $re['school'] = null;
        } else {
            $re['site'] = $school->t_city;
            $re['school'] = $school->showInList();
        }
        $re['gender'] = $user->u_sex;
        $user->load('booths');
        $booths = null;
        if (!empty($user->booths)) {
            foreach ($user->booths as $key => $booth) {
                $booths[] = $booth->showInLogin();
            }
        }
        $re['boohts'] = $booths;
        return $re;
    }
}
