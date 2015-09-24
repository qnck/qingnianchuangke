<?php

/**
*
*/
class WechatPay extends Eloquent
{
    const PAYMENT_TAG = 2;

    private $_wxapi = null;
    private $_wxnotify = null;

    public function __construct()
    {
        $basePath = base_path();
        require_once($basePath."/vendor/wechatpay/lib/WxPay.Api.php");
        require_once($basePath."/vendor/wechatpay/lib/WxPay.Notify.php");

        $this->_wxapi = new WxPayApi();
        $this->_wxnotify = new WxPayNotify();
    }

    public function preOrder($param)
    {
        // var_dump($param);exit;
        $order = new WxPayUnifiedOrder();
        $order->SetOut_trade_no($param['out_trade_no']);
        $order->SetTotal_fee($param['total_fee']*100);
        $order->SetTrade_type('APP');
        $order->SetBody($param['body']);
        $order->SetDetail($param['detail']);
        $order->SetNotify_url('www.54qnck.com');
        return WxPayApi::unifiedOrder($order);
    }
}
