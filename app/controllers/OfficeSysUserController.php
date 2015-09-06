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
        echo '成功';
    }

    public function delUser($id)
    {
        echo '成功';
    }
}
