<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace YeThird\PayGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use YeThird\ThirdSdk;

class Client implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ThirdSdk
     */
    private $Pay;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $response = $this->generateResponseForCode(
            $this->getResultCode(
                $transferObject
            )
        );

        $data = $transferObject->getBody();
        // 四方支付
        $this->Pay = new ThirdSdk([
            'appid' => $data['appid'],
            'secret' => $data['secret'],
            'gateway' => $data['gateway'],
        ]);

        if ($data['TXN_TYPE'] == "A") {
            $response = $this->Pay->c2b([
                'amount' => $data['AMOUNT'],
                'order_no' => $data['INVOICE'],
                'uid' => uniqid(),
                'bank_code' => '20009',
                'notify_url' => 'http://xxxx.com/notify_url',
                'return_url' => 'http://xxxx.com/return_url',
            ]);
        }
        // TODO 获取支付结果
        
        $this->logger->debug(
            [
                'request' => $data,
                'response' => $response
            ]
        );
        
        // return $response;
        return [
            'RESULT_CODE' => self::FAILURE,
            'TXN_ID' => $this->generateTxnId(),
            'response' => $response,
        ];
    }

    /**
     * Generates response
     *
     * @return array
     */
    protected function generateResponseForCode($resultCode)
    {

        return array_merge(
            [
                'RESULT_CODE' => $resultCode,
                'TXN_ID' => $this->generateTxnId()
            ],
            $this->getFieldsBasedOnResponseType($resultCode)
        );
    }

    /**
     * @return string
     */
    protected function generateTxnId()
    {
        return md5(mt_rand(0, 1000));
    }

    /**
     * Returns result code
     *
     * @param TransferInterface $transfer
     * @return int
     */
    private function getResultCode(TransferInterface $transfer)
    {
        $headers = $transfer->getHeaders();

        if (isset($headers['force_result'])) {
            return (int)$headers['force_result'];
        }

        return $this->results[mt_rand(0, 1)];
    }

    /**
     * Returns response fields for result code
     *
     * @param int $resultCode
     * @return array
     */
    private function getFieldsBasedOnResponseType($resultCode)
    {
        switch ($resultCode) {
            case self::FAILURE:
                return [
                    'FRAUD_MSG_LIST' => [
                        'Stolen card',
                        'Customer location differs'
                    ]
                ];
        }

        return [];
    }
}
