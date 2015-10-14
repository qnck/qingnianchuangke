<?php
/**
*
*/
class OfficeSysUserController extends \BaseController
{
    public function listUsers()
    {
        $per_page = Input::get('per_page', 10000000);

        $list = SysUser::paginate($per_page);
        $data = [];
        $data['rows'] = [];
        foreach ($list as $key => $user) {
            $data['rows'][] = $user->showInList();
        }
        $data['total'] = $list->getTotal();
        $re = Tools::reTrue('获取sysuer成功', $data, $list);
        return Response::json($re);
    }

    public function postUser()
    {
        $request = \Request::instance();
        $ip = $request->ip();

        $name = \Input::get('name', '');
        $pass = \Input::get('pass', '');
        $account = \Input::get('account', '');

        try {
            $user = new \SysUser();
            $user->u_name = $name;
            $user->password = $pass;
            $user->account = $account;
            $user->last_local = $ip;
            $user->addUser();
            $re = \Tools::reTrue('添加user成功');
        } catch (Exception $e) {
            $re = \Tools::reFalse($e->getCode(), '添加user失败:'.$e->getMessage());
        }
        return \Response::json($re);
    }

    public function putUser($id)
    {
        $request = Request::instance();
        $ip = $request->ip();

        $name = Input::get('name', '');
        $pass = Input::get('pass', null);
        $account = Input::get('account', '');
        $status= Input::get('status', 1);

        try {
            $user = SysUser::find($id);
            $user->u_name = $name;
            if (!empty($pass)) {
                $user->password = $pass;
            }
            $user->account = $account;
            $user->last_local = $ip;
            $user->status = $status;
            $user->editUser();
            $re = Tools::reTrue('编辑user成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '编辑user失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function delUser($id)
    {
        $user = SysUser::find($id);
        if (!empty($user)) {
            $user->is_del = 1;
            $user->save();
        } else {
            return Tools::reFalse(10003, '删除失败:没有找到用户');
        }
        return Tools::reTrue('删除成功');
    }

    public function addUserRole($id)
    {
        $roles = Input::get('roles', null);
        try {
            SysUserRole::clearUserRole($id);
            if (!empty($roles)) {
                $roles = explode(',', $roles);
                // delete current user role relation
                $admin = SysUser::find($id);
                foreach ($roles as $key => $role) {
                    if (!$role) {
                        continue;
                    }
                    $admin->addRoles($role);
                }
            }
            $re = Tools::reTrue('处理角色成功');
        } catch (Exception $e) {
            $re= Tools::reFalse($e->getCode(), '处理角色失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function delUserRole()
    {
        $roles = Input::get('roles', '');
        try {
            $admin = SysUser::find($id);
            $roles = explode(',', $roles);
            foreach ($roles as $id) {
                if (!is_numeric($id)) {
                    throw new Exception("包含无效的角色数据-".$id, 10001);
                }
            }
            $admin->delRoles($roles);
            $re = Tools::reTrue('删除角色成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '删除角色失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function enableUser($id)
    {
        $enable = Input::get('enable', 1);

        try {
            $admin = SysUser::find($id);
            if (empty($admin)) {
                throw new Exception("没有找到请求的用户", 10001);
            }
            $admin->status = $enable;
            $admin->save();
            $re = Tools::reTrue('操作成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '操作失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function listUserRole($id)
    {
        $per_page = Input::get('per_page', null);

        try {
            $admin = SysUser::find($id);
            if (empty($admin)) {
                throw new Exception("没有找到用户", 10001);
            }
            $query = SysRole::select('sys_roles.*')->join('sys_user_roles', function ($q) use ($id) {
                $q->on('sys_roles.id', '=', 'sys_user_roles.r_id')->where('sys_user_roles.admin_id', '=', $id);
            });
            $list = $query->paginate($per_page);
            $data['rows'] = [];
            foreach ($list as $key => $role) {
                $data['rows'][] = $role->showInList();
            }
            $data['total'] = $list->getTotal();
            $re = Tools::reTrue('获取角色成功', $data, $list);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取角色失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
