<?php

namespace YeThird\PayGateway\Controller\Payment;

use Magento\Payment\Helper\Data as PaymentHelper;
use YeThird\ThirdSdk;

abstract class AbstractPaystackStandard extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface 
     */
    protected $orderRepository;
    
    /**
     *
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $orderInterface;
    protected $checkoutSession;
    protected $method;
    protected $messageManager;
    
    /**
     *
     * @var \YeThird\PayGateway\Model\Ui\ConfigProvider 
     */
    protected $configProvider;
    
    /**
     *
     * @var ThirdSdk 
     */
    protected $thirdSdk;
    
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;
    
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     *
     * @var \Magento\Framework\App\Request\Http 
     */
    protected $request;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            \Magento\Sales\Api\Data\OrderInterface $orderInterface,
            \Magento\Checkout\Model\Session $checkoutSession,
            PaymentHelper $paymentHelper,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \YeThird\PayGateway\Model\Ui\ConfigProvider $configProvider,
            \Magento\Framework\Event\Manager $eventManager,
            \Magento\Framework\App\Request\Http $request,
            \Psr\Log\LoggerInterface $logger
    ) {
        
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->orderInterface = $orderInterface;
        $this->checkoutSession = $checkoutSession;
        $this->method = $paymentHelper->getMethodInstance(\YeThird\PayGateway\Model\Payment\ThirdPay::CODE);
        $this->messageManager = $messageManager;
        $this->configProvider = $configProvider;
        $this->eventManager = $eventManager;
        $this->request = $request;
        $this->logger = $logger;
        
        $this->paystack = $this->initThridPayPHP();
        
        
        parent::__construct($context);
    }
    
    protected function initThridPayPHP() {
        $options = [
            'appid' => $this->method->getConfigData('appid'),
            'secret' => $this->method->getConfigData('secret'),
            'gateway' => $this->method->getConfigData('gateway'),
        ];
        return new ThirdSdk($options);
    }
    
    protected function redirectToFinal($successFul = true, $message="") {
        if($successFul){
            if($message) $this->messageManager->addSuccessMessage(__($message));
            return $this->_redirect('checkout/onepage/success');
        } else {
            if($message) $this->messageManager->addErrorMessage(__($message));
            return $this->_redirect('checkout/onepage/failure');
        }
    }
}
