<?php

/**
 * 微信 业务BUSINESS
 * @author Kydz 2014.04.02
 */
class WechatApi extends Eloquent
{

    private $_access_token = null;
    private $_requester = null;
    private $_api_add = 'https://api.weixin.qq.com/cgi-bin/';
    private $_token_key;

    public function __construct($app)
    {
        $this->_requester = new CurlRequest();
        $config = Config::get('app.wechat');
        if (empty($config[$app])) {
            throw new Exception('没有找到微信APP ['.$app.'] 的相关配置', 20001);
        }
        $this->_appid = $config[$app]['id'];
        $this->_appsec = $config[$app]['key'];
        $this->getAccessToken();
    }

    /**
     * [_getAccessToken 获取access_token]
     * @author Kydz 2014.04.02
     * @return [type] [description]
     */
    private function getAccessToken()
    {
        $url = $this->_api_add.'token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_appsec;
        $re = $this->_requester->get($url);
        if ($re['err']) {
            throw new Exception($re['errmsg'], 20001);
        }
        // var_dump($re);exit;
        $re = json_decode($re['content']);
        if (isset($re->errcode) && $re->errmsg != '') {
            throw new Exception($re->errmsg, 20001);
        } else {
            $access_token = $re->access_token;
            $expire = $re->expires_in;
            $this->_access_token = $access_token;
            return true;
        }
    }

    /**
     * [setMenu 设置菜单]
     * @author Kydz 2014.04.02
     */
    public function setMenu()
    {
        $config = [];
        $url = $this->_api_add.'menu/create?access_token='.$this->_access_token;
        $re = $this->_requester->post($url, $config);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [getMenu 获取菜单]
     * @author Kydz 2014.04.02
     * @return [type] [description]
     */
    public function getMenu()
    {
        $url = $this->_api_add.'menu/get?access_token='.$this->_access_token;
        $re = $this->_requester->get($url);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [delMenu 删除菜单]
     * @author Kydz 2014.04.02
     * @return [type] [description]
     */
    public function delMenu()
    {
        $url = $this->_api_add.'menu/delete?access_token='.$this->_access_token;
        $re = $this->_requester->get($url);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [addGroups 添加分组]
     * @author Kydz 2014.04.02
     * @param [type] $name [分组名称]
     */
    public function addGroups($name)
    {
        $data = [
            'group' => [
                'name' => $name,
                ],
            ];
        $data = json_encode($data);
        $url = $this->_api_add.'groups/create?access_token='.$this->_access_token;
        $re = $this->_requester->post($url, $data);
        return $re;
    }

    /**
     * [getGroups 获取分组]
     * @author Kydz 2014.04.02
     * @return [type] [description]
     */
    public function getGroups()
    {
        $url = $this->_api_add.'groups/get?access_token='.$this->_access_token;
        $re = $this->_requester->get($url);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [getUserGroup 获取用户所在分组]
     * @param  [type] $openid [用户的微信唯一标识符]
     * @author Kydz 2014.04.02
     * @return [type]         [description]
     */
    public function getUserGroup($openid)
    {
        $url = $this->_api_add.'groups/getid?access_token='.$this->_access_token;
        $data = ['openid' => $openid];
        $data = json_encode($data);
        $re = $this->_requester->post($url, $data);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [editGroupName 编辑分组名称]
     * @param  [type] $group_id [分组ID]
     * @param  [type] $name     [名称]
     * @author Kydz 2014.04.02
     * @return [type]           [description]
     */
    public function editGroupName($group_id, $name)
    {
        $url = $this->_api_add.'groups/update?access_token='.$this->_access_token;
        $data = [
            'group' => [
                'id' => $group_id,
                'name' => $name,
                ],
            ];
        $data = json_encode($data);
        $re = $this->_requester->post($url, $data);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [moveUserGroup 移动用户分组]
     * @param  [type] $openid   [用户微信唯一标识符]
     * @param  [type] $group_id [分组ID]
     * @author Kydz 2014.04.02
     * @return [type]           [description]
     */
    public function moveUserGroup($openid, $group_id)
    {
        $url = $this->_api_add.'groups/members/update?access_token='.$this->_access_token;
        $data = [
            'openid' => $openid,
            'to_groupid' => $group_id,
            ];
        $data = json_encode($data);
        $re = $this->_requester->post($url, $data);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [getUser 获取用户信息]
     * @param  [type] $openid [用户微信唯一标识符]
     * @author Kydz 2014.04.02
     * @return [type]         [description]
     */
    public function getUser($openid)
    {
        $url = $this->_api_add.'user/info?access_token='.$this->_access_token.'&openid='.$openid.'&lang=zh_CN';
        $re = $this->_requester->get($url);
        $re = json_decode($re['content'], true);
        return $re;
    }

    public function getUsers($openids)
    {
        $url = $this->_api_add.'user/info/batchget?access_token='.$this->_access_token;
        $users = [];
        $user_list = [];
        foreach ($openids as $key => $openid) {
            $tmp = ['openid' => $openid, 'lang' => 'zh-CN'];
            $users[] = $tmp;
            if (count($users) == 100) {
                $user_list[] = $users;
                $users = [];
            }
        }
        $re = [];
        foreach ($user_list as $key => $list) {
            $data = ['user_list' => $list];
            $response = $this->_requester->post($url, json_encode($data));
            $response = json_decode($response['content'], true);
            $re = array_merge($re, $response['user_info_list']);
        }
        return $re;
    }

    /**
     * [getFollowUser 获取关注用户列表]
     * @author Kydz 2014.04.02
     * @return [type] [description]
     */
    public function getFollowUser()
    {
        $url = $this->_api_add.'user/get?access_token='.$this->_access_token.'&next_openid=';
        $re = $this->_requester->get($url);
        $re = json_decode($re['content'], true);
        return $re;
    }

    /**
     * [getQrcodeTicket 获取二维码票据]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function getQrcodeTicket($data)
    {
        $data = json_encode($data);
        $url = $this->_api_add.'qrcode/create?access_token='.$this->_access_token;
        $re = $this->_requester->post($url, $data);
        $re = json_decode($re['content'], true);
        $ticket = $re['ticket'];
        return $ticket;
    }

    /**
     * [getQrcode 获取二维图形]
     * @param  [type] $ticket [二维码票据]
     * @return [type]         [description]
     */
    private function getQrcode($ticket)
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
        header('Location:'.$url);
    }

    /**
     * [getTpqrcode 获取临时二维码]
     * @param  [type] $flag [二维码参数]
     * @return [type]       [description]
     */
    public function getTpqrcode($flag)
    {
        $data = [
            'expire_seconds' => 1800,
            'action_name' => 'QR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => $flag,
                    ],
                ],
            ];
        $ticket = $this->getQrcodeTicket($data);
        $this->getQrcode($ticket);
    }

    /**
     * [getPsqrcode 获取永久二维码]
     * @param  [type] $flag [二维码参数]
     * @return [type]       [description]
     */
    public function getPsqrcode($flag)
    {
        $data = [
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => $flag,
                    ],
                ],
            ];
        $ticket = $this->getQrcodeTicket($data);
        $this->getQrcode($ticket);
    }
}
