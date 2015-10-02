<?php
/**
*
*/
class SysRole extends Eloquent
{
    public $timestamps = false;

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['name'] = $this->r_name;
        $data['desc'] = $this->r_memo;
        return $data;
    }

    public function addMenu($menu)
    {
        $role_menu = new SysRoleMenu();
        $role_menu->r_id = $this->id;
        $role_menu->m_id = $menu;
        return $role_menu->save();
    }

    public function delMenus($menus)
    {
        return DB::table('sys_role_menus')->where('r_id', '=', $this->id)->whereIn('m_id', $menus)->delete();
    }
}
