<?php

/**
*
*/
class WechatPay extends Eloquent
{
    const PAYMENT_TAG = 2;

    public $_notify = null;
    private $_notify_url = '';

    public function __construct()
    {
        $basePath = base_path();
        require_once($basePath."/vendor/wechatpay/lib/WxPay.Api.php");

        $this->_notify_url = Config::get('app.pay.wechat.notify_url');
        $this->_notify = new WxPayNotifyReply();
    }

    public function preOrder($param)
    {
        $order = new WxPayUnifiedOrder();
        $order->SetOut_trade_no($param['out_trade_no']);
        $order->SetTotal_fee($param['total_fee']*100);
        $order->SetTrade_type('APP');
        $order->SetBody($param['body']);
        $order->SetDetail($param['detail']);
        $order->SetNotify_url($this->_notify_url);
        $re = WxPayApi::unifiedOrder($order);
        if ($re['result_code'] == 'FAIL') {
            throw new Exception("微信 preorder 错误-".$re['err_code_des'], 9001);
        }
        if ($re['return_code'] == 'FAIL') {
            throw new Exception("微信 preorder 错误-".$re['return_msg'], 9001);
        }
        return $re;
    }

    public function verifyNotify()
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

        $re = WxPayResults::Init($xml);
        if ($re['return_code'] == 'FAIL') {
            throw new Exception("微信 preorder 错误-".$re['return_msg'], 9001);
        }
        return $re;
    }
}
