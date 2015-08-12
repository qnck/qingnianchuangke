<?php
/**
*
*/
namespace Admin;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;

class SysUserController extends \BaseController
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
        return Response::json($re);
    }

    public function postUser()
    {
        echo '成功';
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
