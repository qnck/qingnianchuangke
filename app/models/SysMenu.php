<?php
/**
*
*/
class SysMenu extends Eloquent
{
    public $timestamps = false;

    private function baseValidate()
    {
        $validator = Validator::make(
            ['name' => $this->m_name, 'desc' => $this->m_memo, 'parent' => $this->m_parent, 'url' => $this->m_url],
            ['name' => 'required', 'desc' => 'required', 'parent' => 'required', 'url' => 'required']
        );
        if ($validator->fails()) {
            $msg = $validator->messages();
            throw new Exception($msg->first(), 1);
        } else {
            return true;
        }
    }

    public function addMenu()
    {
        $this->baseValidate();
        return $this->save();
    }

    public function showInList()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['name'] = $this->m_name;
        $data['desc'] = $this->m_memo;
        $data['url'] = $this->m_url;
        $data['parent'] = (int)$this->m_parent;
        $data['level'] = (int)$this->m_level;
        $data['path'] = $this->m_path;
        $data['children'] = [];

        return $data;
    }

    public static function makeTree()
    {
        $admin_id = Tools::getAdminId();
        $admin = SysUser::find($admin_id);
        if (empty($admin)) {
            throw new Exception("没有查找到admin数据", 10003);
        }
        if ($admin->account == 'root') {
            $list = SysMenu::get();
        } else {
            $list = SysMenu::leftJoin('sys_role_menus', function ($q) {
                $q->on('sys_menus.id', '=', 'sys_role_menus.m_id');
            })->leftJoin('sys_user_roles', function ($q) use ($admin_id) {
                $q->on('sys_user_roles.id', '=', 'sys_role_menus.r_id')->where('sys_user_roles.admin_id', '=', $admin_id);
            })->groupBy('sys_menus.id')->get();
        }
        $levels = [];
        $tree = [];
        $trash = [];
        $max_level = 0;
        foreach ($list as $key => $menu) {
            $levels[$menu->m_level][$menu->id] = $menu->showInList();
            $max_level = $max_level > $menu->m_level ? $max_level : $menu->m_level;
        }
        for ($max_level; $max_level > 0; $max_level--) {
            $current_level = $max_level;
            $upper_level = $current_level - 1;
            foreach ($levels[$current_level] as $key => $menu) {
                if (array_key_exists($menu['parent'], $levels[$upper_level])) {
                    $levels[$upper_level][$menu['parent']]['children'][] = $menu;
                } else {
                    $trash[] = $menu;
                }
                unset($levels[$current_level][$key]);
            }
        }
        $tree = array_values($levels[0]);
        return $tree;
    }
}
