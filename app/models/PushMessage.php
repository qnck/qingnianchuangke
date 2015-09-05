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
        $this->_u_id = $u_id;
    }


    public function push($content)
    {
        // $this->baseValidate();
        $this->initCurl();
        $send = ['where' => ['u_id' => $this->_u_id], 'data' => ['alert' => $content]];
        $data = json_encode($data);
        $this->setPostData($data);
        $re = $this->execCurl();
        if ($re->returnstatus == 'Success') {
            return true;
        } else {
            if (is_object($re)) {
                throw new Exception($re->message, 1);
            } else {
                throw new Exception("推送消息失败", 1);
            }
        }
    }

    /**
     * prepare curl connection
     * @author Kydz 2015-06-14
     * @return n/a
     */
    private function initCurl()
    {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->_send_url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['X-AVOSCloud-Application-Id: cw7h66rl18j9gbftcrqhdkqi16xmezj8wd7d2fxjs5b2qll', 'X-AVOSCloud-Application-Key: 3beeo9ca54fln7xynpy81neyv9trrl4zfe8n294g0hpx4p5t', 'Content-Type: application/json']);
    }
    
    /**
     * execute curl
     * @author Kydz 2015-06-14
     * @return object xml to obj
     */
    private function execCurl()
    {
        $re = curl_exec($this->ch);
        $re = new SimpleXMLElement($re);
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
}
