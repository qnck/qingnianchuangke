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
            ['name' => $this->m_name, 'parent' => $this->m_parent, 'url' => $this->m_url],
            ['name' => 'required', 'parent' => 'required', 'url' => 'required']
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

    public static function getTreeByAdmin($admin_id = -1)
    {
        if ($admin_id == -1) {
            $admin = new SysUser();
            $admin->account = 'root';
        } else {
            $admin = SysUser::find($admin_id);
            if (empty($admin)) {
                throw new Exception("没有查找到admin数据", 10003);
            }
        }
        if ($admin->account == 'root') {
            $list = SysMenu::get();
        } else {
            $list = SysMenu::select('sys_menus.*')->join('sys_role_menus', function ($q) {
                $q->on('sys_menus.id', '=', 'sys_role_menus.m_id');
            })->join('sys_user_roles', function ($q) use ($admin_id) {
                $q->on('sys_user_roles.id', '=', 'sys_role_menus.r_id')->where('sys_user_roles.admin_id', '=', $admin_id);
            })->groupBy('sys_menus.id')->get();
        }
        $re = SysMenu::makeTree($list);
        return $re['tree'];
    }

    public static function getTreeByRole($role_id = 0)
    {
        $role = SysRole::find($role_id);
        if (empty($role)) {
            throw new Exception("没有查找到角色数据", 10001);
        }
        $list = SysMenu::select('sys_menus.*')->join('sys_role_menus', function ($q) use ($role_id) {
            $q->on('sys_menus.id', '=', 'sys_role_menus.m_id')->where('sys_role_menus.r_id', '=', $role_id);
        })->groupBy('sys_menus.id')->get();
        $re = SysMenu::makeTree($list);
        return $re['tree'];
    }

    public static function makeTree($list)
    {
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
        return ['tree' => $tree, 'trash' => $trash];
    }
}
