<?php

namespace YeThird\PayGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use YeThird\PayGateway\Gateway\Http\Client\Client;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\Store as Store;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ye_gateway';
    
    protected $method;

    public function __construct(PaymentHelper $paymentHelper, Store $store)
    {
        $this->method = $paymentHelper->getMethodInstance(\YeThird\PayGateway\Model\Payment\ThirdPay::CODE);
        $this->store = $store;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        Client::SUCCESS => __('Success'),
                        Client::FAILURE => __('Fraud')
                    ],
                    'api_url' => $this->store->getBaseUrl() . 'rest/',
                ]
            ]
        ];
    }
}
