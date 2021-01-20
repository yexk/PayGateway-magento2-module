<?php

namespace YeThird\PayGateway\Model;

use Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Payment\Helper\Data as PaymentHelper;
use YeThird\PayGateway\Model\Payment\ThirdPay as ThirdPayModel;
use YeThird\ThirdSdk;

class PaymentManagement implements \YeThird\PayGateway\Api\PaymentManagementInterface
{
    protected $paymentInstance;

    protected $ThirdSdkLib;

    protected $orderInterface;
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $eventManager;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        PaymentHelper $paymentHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        Request $request
    ) {
        $this->request = $request;
        $this->eventManager = $eventManager;
        $this->paymentInstance = $paymentHelper->getMethodInstance(ThirdPayModel::CODE);

        $this->orderInterface = $orderInterface;
        $this->checkoutSession = $checkoutSession;
        $options = [
            'appid' => $this->paymentInstance->getConfigData('appid'),
            'secret' => $this->paymentInstance->getConfigData('secret'),
            'gateway' => $this->paymentInstance->getConfigData('gateway'),
        ];

        $this->ThirdSdkLib = new ThirdSdk($options);
    }

    /**
     * @param string $reference
     * @return bool
     */
    public function verifyPayment($quoteId)
    {
        $data = $this->request->getBodyParams();
        try {
            $order = $this->getOrder();
            //return json_encode($transaction_details);
            if ($order && $order->getQuoteId() === $quoteId) {
                
                $data = $this->ThirdSdkLib->c2b([
                    'amount' => $data['amount'],
                    'order_no' => $quoteId,
                    'uid' => uniqid(),
                    'bank_code' => '20009',
                    'notify_url' => $data['notify_url'],
                    'return_url' => $data['return_url'],
                ]);
                
                $this->eventManager->dispatch('ye_gateway_payment_verify_after', [
                    "thirdpay_order" => $order,
                ]);
                
                return $data;
            }
        } catch (Exception $e) {
            return json_encode([
                'status'=>0,
                'message'=>$e->getMessage()
            ]);
        }
        return json_encode([
            'status'=>0,
            'message'=>"quoteId doesn't match transaction"
        ]);
    }

    /**
     * Loads the order based on the last real order
     * @return boolean
     */
    private function getOrder()
    {
        // get the last real order id
        $lastOrder = $this->checkoutSession->getLastRealOrder();
        if ($lastOrder) {
            $lastOrderId = $lastOrder->getIncrementId();
        } else {
            return false;
        }

        if ($lastOrderId) {
            // load and return the order instance
            return $this->orderInterface->loadByIncrementId($lastOrderId);
        }
        return false;
    }
}
