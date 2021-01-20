<?php

namespace YeThird\PayGateway\Controller\Payment;

class Setup extends AbstractPaystackStandard {

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        $this->logger->debug($this->request->getContent());
        /* 
        $message = '';
        $order = $this->orderInterface->loadByIncrementId($this->checkoutSession->getLastRealOrder()->getIncrementId());
        if ($order && $this->method->getCode() == $order->getPayment()->getMethod()) {

            try {
                return $this->processAuthorization($order);
            } catch (\Yabacon\Paystack\Exception\ApiException $e) {
                $message = $e->getMessage();
                $order->addStatusToHistory($order->getStatus(), $message);
                $this->orderRepository->save($order);
            }
        }

        $this->redirectToFinal(false, $message); */
    }

    protected function processAuthorization(\Magento\Sales\Model\Order $order) {
        $tranx = $this->paystack->transaction->initialize([
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'amount' => $order->getGrandTotal() * 100, // in kobo
            'email' => $order->getCustomerEmail(), // unique to customers
            'reference' => $order->getIncrementId(), // unique to transactions
            'currency' => $order->getCurrency(),
            'callback_url' => $this->configProvider->store->getBaseUrl() . "paystack/payment/callback",
            'metadata' => array('custom_fields' => array(
                array(
                    "display_name"=>"Plugin",
                    "variable_name"=>"plugin",
                    "value"=>"magento-2"
                )
            )) 
        ]);

        //var_dump($tranx); die();

        $redirectFactory = $this->resultRedirectFactory->create();
        $redirectFactory->setUrl($tranx->data->authorization_url);


        return $redirectFactory;
    }

}
