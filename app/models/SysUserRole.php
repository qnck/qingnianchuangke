<?php
/**
*
*/
class SysUserRole extends Eloquent
{
    public $timestamps = false;

    public static function clearUserRole($admin_id)
    {
        return DB::table('sys_user_roles')->where('admin_id', '=', $admin_id)->delete();
    }

    // laravel relation
    public function user()
    {
        return $this->hasOne('SysUser', 'id', 'admin_id');
    }
}
