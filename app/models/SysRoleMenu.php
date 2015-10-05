<?php
/**
*
*/
class SysRoleMenu extends Eloquent
{
    public $timestamps = false;

    public static function clearRoleMenu($role_id)
    {
        return DB::table('sys_role_menus')->where('r_id', '=', $role_id)->delete();
    }
}
