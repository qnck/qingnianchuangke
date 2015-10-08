<?php
/**
*
*/
class OfficeController extends \BaseController
{
    public function getMenu()
    {
        try {
            $admin_id = Tools::getAdminId();
            $menu_tree = SysMenu::getTreeByAdmin($admin_id);
            $re = Tools::reTrue('获取菜单成功', $menu_tree);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取菜单失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
    
    public function login()
    {
        $account = Input::get('account', '');
        $pass = Input::get('pass', '');

        try {
            $admin = SysUser::where('account', '=', $account)->where('is_del', '=', 0)->where('status', '=', 1)->first();
            if (empty($admin)) {
                throw new Exception("没有找到可用的用户", 10003);
            }
            if (!Hash::check($pass, $admin->password)) {
                throw new Exception("密码错误", 10003);
            }
            Session::put('admin_id', $admin->id);
            $re = Tools::reTrue('登录成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '登录失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function logout()
    {
        Session::forget('admin_id');
        $re = Tools::reTrue('登出成功');
        return Response::json($re);
    }
}
