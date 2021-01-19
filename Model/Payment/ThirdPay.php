<?php

namespace YeThird\PayGateway\Model\Payment;

class ThirdPay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'ye_gateway';

    protected $_code = self::CODE;
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}
