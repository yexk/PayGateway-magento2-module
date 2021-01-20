<?php

namespace YeThird\PayGateway\Controller\Payment;

use Magento\Sales\Model\Order;

class Recreate extends AbstractPaystackStandard {

    public function execute() {
        $this->logger->debug($this->request->getContent());
        /* 
        $order = $this->checkoutSession->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation("Payment failed or cancelled")->save();
            
        }
        
        $this->checkoutSession->restoreQuote();
        $this->_redirect('checkout', ['_fragment' => 'payment']); */
    }

}
