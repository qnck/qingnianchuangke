<?php

/**
*
*/

class Alipay
{
    const PAYMENT_TAG = 2;

    public function verifyNotify()
    {
        $basePath = base_path();
        require_once($basePath."/vendor/alipay/alipay.config.php");
        require_once($basePath."/vendor/alipay/lib/alipay_notify.class.php");

        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if (!$verify_result) {
            throw new Exception("支付宝验证失败", 1);
        } else {
            return true;
        }
    }
}
