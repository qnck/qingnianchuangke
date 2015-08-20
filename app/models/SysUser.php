<?php
/**
*
*/
class SysUser extends \Eloquent
{
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['u_name'] = $this->u_name;
        $data['last_local'] = $this->last_local;
        $data['last_time'] = $this->last_time;
        $data['status'] = $this->status;
        $data['is_del'] = $this->is_del;
        
        return $data;
    }

    private function baseValidate()
    {
        $validator = Validator::make(
            ['account' => $this->account, 'name' => $this->u_name, 'pass' => $this->password],
            ['account' => 'required', 'name' => 'required', 'pass' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addUser()
    {
        $now = new DateTime();
        $this->baseValidate();
        // chk exist
        $chk = SysUser::where('account', '=', $this->account);
        if (!empty($chk)) {
            throw new Exception("账号已经存在", 10001);
        }
        $this->password = Hash::make($this->u_password);
        $this->last_time = $now->format('Y-m-d H:i:s');
        $this->status = 1;
        $this->is_del = 0;
        $this->save();
        return $this->id;
    }
}
