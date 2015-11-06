<?php
/**
*
*/
class ReplyController extends \BaseController
{
    public function getRelatedRelpies()
    {
        $cate = Input::get('cate', '');
        $id = Input::get('id', 0);
        $last_id = Input::get('last_id', 0);
        $per_page = Input::get('per_page', 30);

        $mapping = Reply::getRepliableCate();

        try {
            if (array_key_exists($cate, $mapping)) {
                $cate = $mapping[$cate];
            } else {
                throw new Exception("需要传入有效的评论分类", 2001);
            }

            $query = Reply::select('replies.*')->where('replies.to_id', '=', 0)->where('replies.status', '=', 1);
            if ($last_id) {
                $query = $query->where('replies.id', '<', $last_id);
            }
            $query = $query
            ->join('repliables', function ($q) use ($cate, $id) {
                $q->on('repliables.reply_id', '=', 'replies.id')->where('repliables.repliable_type', '=', $cate)->where('repliables.repliable_id', '=', $id);
            });
            $list = $query->orderBy('replies.id', 'DESC')->paginate($per_page);
            $ids = [];
            foreach ($list as $key => $reply) {
                $ids[] = $reply->id;
            }
            $children = Reply::whereIn('to_id', $ids)->where('status', '=', 1)->orderBy('created_at', 'DESC')->get();
            $all = $list->merge($children);
            $data = Reply::makeTree($all);
            $re = Tools::reTrue('获取评论成功', $data);
        } catch (Exception $e) {
            $re = Tools::reFalse($e->getCode(), '获取评论失败:'.$e->getMessage());
        }
        return Response::json($re);
    }
}
