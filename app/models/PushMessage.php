<?php
/**
*
*/
class PushMessage extends Eloquent
{
    public $timestamps = false;

    private $_u_id = 0;
    private $_send_url = 'https://leancloud.cn/1.1/push';

    public function __construct($u_id)
    {
        if (!$u_id) {
            throw new Exception("需要传入有效地的消息接收人", 2002);
            
        }
        $this->_u_id = $u_id;
    }


    public function pushMessage($content)
    {
        // $this->baseValidate();
        $this->initCurl();
        $data = ["fun" => 0, "action" => "makerck.PUSH", "alert" => $content];
        $send = ['where' => ['userId' => (string)$this->_u_id], 'data' => $data];
        $send = json_encode($send);
        $this->setPostData($send);
        $re = $this->execCurl();
        $this->addNotification($content);
        if (!empty($re->objectId)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * prepare curl connection
     * @author Kydz 2015-06-14
     * @return n/a
     */
    private function initCurl()
    {
        $config = Config::get('app.leancloud');
        if (!$config['id'] || !$config['key']) {
            throw new Exception("没有有效的leancloud配置", 2002);
        }
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->_send_url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['X-AVOSCloud-Application-Id:'.$config['id'], 'X-AVOSCloud-Application-Key:'.$config['key'], 'Content-Type: application/json']);
    }
    
    /**
     * execute curl
     * @author Kydz 2015-06-14
     * @return object xml to obj
     */
    private function execCurl()
    {
        $re = curl_exec($this->ch);
        $re = json_decode($re);
        return $re;
    }

    /**
     * set post data
     * @author Kydz 2015-06-14
     * @param  array $data data to be posted
     */
    private function setPostData($data)
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
    }

    private function addNotification($content)
    {
        $not = new Notification();
        $not->n_icon = '';
        $not->n_title = '推送消息';
        $not->n_brief = $content;
        $not->n_content = $content;
        $not->n_url = '';
        $not->n_type = 2;
        $not->n_cate = 0;
        $not->addNot();
        $receiver = new NotificationReceiver();
        $receiver->n_id = $not->n_id;
        $receiver->to_id = $this->_u_id;
        $receiver->to_type = 1;
        $receiver->save();
    }
}
