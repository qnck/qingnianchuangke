<?php
/**
*
*/
class ImgController extends \BaseController
{
    public function postImg()
    {
        $cate = Input::get('cate', '');
        $token = Input::get('img_token', '');

        try {
            $oss = new AliyunOss($cate, $token);
            $data = $oss->upload();
            $re = Tools::reTrue('上传图片成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '上传图片失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
