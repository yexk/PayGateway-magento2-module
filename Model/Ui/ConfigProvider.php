<?php

namespace YeThird\PayGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use YeThird\PayGateway\Gateway\Http\Client\Client;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ye_gateway';

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
                    ]
                ]
            ]
        ];
    }
}
