<?php
/**
*
*/
class ProductController extends \BaseController
{
    public function postPraise($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            if (empty($product)) {
                throw new Exception("请求的商品不存在", 2001);
            }
            $chk = $product->praises()->where('praises.u_id', '=', $u_id)->first();
            if (!empty($chk)) {
                throw new Exception("已经赞过了", 7001);
            }
            $data = [
                'u_id' => $u_id,
                'created_at' => Tools::getNow(),
                'u_name' => $user->u_name
            ];
            $praise = new Praise($data);
            $product->praises()->save($praise);
            $product->p_praise_count++;
            $product->save();
            $re = Tools::reTrue('点赞成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '点赞失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postFavorite($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            if (empty($product)) {
                throw new Exception("请求的商品不存在", 2001);
            }
            $chk = $product->favorites()->where('favorites.u_id', '=', $u_id)->first();
            if (!empty($chk)) {
                throw new Exception("已经收藏过了", 7001);
            }
            $data = [
                'u_id' => $u_id,
                'created_at' => Tools::getNow(),
                'u_name' => $user->u_name
            ];
            $favorite = new Favorite($data);
            $product->favorites()->save($favorite);
            $re = Tools::reTrue('收藏成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '收藏失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function delFavorite($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);

        try {
            $user = User::chkUserByToken($token, $u_id);
            $product = Product::find($id);
            if (empty($product)) {
                throw new Exception("请求的商品不存在", 2001);
            }
            $chk = $product->favorites()->where('favorites.u_id', '=', $u_id)->first();
            if (empty($chk)) {
                throw new Exception("已经取消收藏了", 7001);
            }
            $product->favorites()->detach($chk->id);
            $chk->delete();
            $re = Tools::reTrue('取消收藏成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '取消收藏失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function getProduct($id)
    {
        $u_id = Input::get('u_id');

        try {
            $product = Product::with([
                'quantity',
                'promo',
                'replies',
                'favorites' => function ($q) {
                    $q->where('favorites.u_id', '=', $this->u_id);
                }
                ])->find($id);
            if (!empty($product->promo)) {
                $product->promo->load('praises');
            }
            if (empty($product->p_id)) {
                throw new Exception("无法找到请求的产品", 7001);
            }
            $data = $product->showDetail();
            $re = Tools::reTrue('获取产品成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取产品失败:'.$e->getMessage());
        }
        return Response::json($re);
    }

    public function postReply($id)
    {
        $token = Input::get('token', '');
        $u_id = Input::get('u_id', 0);
        $content = Input::get('content', '');
        $to_u_id = Input::get('to', 0);
        $to = Input::get('parent', 0);

        try {
            $product = Product::find($id);
            $product->p_reply_count += 1;

            $user = User::chkUserByToken($token, $u_id);

            $to_user = User::find($to_u_id);
            if (empty($to_user)) {
                $to_u_id = 0;
                $to_u_name = '';
            } else {
                $to_u_name = $to_user->u_nickname;
            }
            $data = [
                'to_id' => $to,
                'created_at' => Tools::getNow(),
                'content' => $content,
                'u_id' => $u_id,
                'u_name' => $user->u_nickname,
                'status' => 1,
                'to_u_id' => $to_u_id,
                'to_u_name' => $to_u_name,
            ];
            $reply = new Reply($data);
            $product->replies()->save($reply);
            $product->save();
            $re = Tools::reTrue('回复成功');
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '回复失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
