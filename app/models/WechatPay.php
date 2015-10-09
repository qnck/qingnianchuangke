<?php

/**
*
*/
class WechatPay
{
    const PAYMENT_TAG = 2;

    public $_notify = null;
    private $_notify_url = '';
    public $log = null;

    public function __construct()
    {
        $basePath = base_path();
        require_once($basePath."/vendor/wechatpay/lib/WxPay.Api.php");
        require_once($basePath."/vendor/wechatpay/lib/log.php");

        $this->_notify_url = Config::get('app.pay.wechat.notify_url');
        $this->_notify = new WxPayNotifyReply();
        $logHandler= new CLogFileHandler($basePath."/vendor/wechatpay/logs/".date('Y-m-d').'.log');
        $this->log = WXPayLog::Init($logHandler, 15);
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
        $xml = file_get_contents('php://input', 'r');

        $re = WxPayResults::Init($xml);
        $this->log->INFO('RESULTS FROM XML:'.json_encode($re));
        if ($re['return_code'] == 'FAIL') {
            $this->log->ERROR($re['return_msg'].'|'.$re['err_code_des']);
            throw new Exception("微信 preorder 错误-".$re['return_msg'], 9001);
        }
        return $re;
    }
}
