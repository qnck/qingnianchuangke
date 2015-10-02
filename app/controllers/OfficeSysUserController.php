<?php
/**
*
*/
class OfficeSysUserController extends \BaseController
{
    public function listUsers()
    {
        $list = \SysUser::paginate(30);

        $data = [];
        $data['rows'] = [];
        $data['total'] = $list->count();
        foreach ($list as $key => $user) {
            $data['rows'][] = $user->showInList();
        }
        $re = \Tools::reTrue('获取sysuer成功', $data);
        return \Response::json($re);
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
        $pass = Input::get('pass', '');
        $account = Input::get('account', '');
        $status= Input::get('status', 1);

        try {
            $user = SysUser::find($id);
            $user->u_name = $name;
            $user->password = $pass;
            $user->account = $account;
            $user->last_local = $ip;
            $user->status = $status;
            $user->save();
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
        $roles = Input::get('roles', '');
        try {
            $roles = explode(',', $roles);
            $admin = SysUser::find($id);
            foreach ($roles as $key => $role) {
                $admin->addRoles($role);
            }
            $re = Toole::reTrue('添加角色成功');
        } catch (Exception $e) {
            $re= Toole::reFalse($e->getCode(), '添加角色失败:'.$e->getMessage());
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
}
