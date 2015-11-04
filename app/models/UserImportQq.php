<?php
/**
*
*/
class UserImportQq extends UserImport
{
    public $primaryKey = 'u_id';
    public $timestamps = false;

    public function import()
    {
        $this->baseValidate();
        $chk = UserImportQq::where('u_ext_id', '=', $this->u_ext_id)->first();
        if (empty($chk)) {
            $re = $this->register($this->ext_id, $this->ext_token, $this->u_nickname, $this->u_head_img, $this->u_gender);
            $this->u_id = $re['u_id'];
            $this->created_at = Tools::getNow();
            $this->save();
            $this->u_id = $re['u_id'];
        } else {
            $this->u_id = $chk->u_id;
        }
        $re = $this->login($this->u_id);
        $re['import_type'] = 'qq';
        return $re;
    }
}
