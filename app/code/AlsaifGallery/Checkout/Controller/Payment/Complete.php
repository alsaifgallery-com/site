<?php

namespace AlsaifGallery\Checkout\Controller\Payment;

/**
 * Class Fail
 */
class Complete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CheckoutApi
     */
    public $apiHandler;

    /**
     * @var Logger
     */
    public $logger;
    
    protected $quoteRepository;
    
    protected $resultJsonFactory;

    /**
     * PlaceOrder constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \CheckoutCom\Magento2\Model\Service\ApiHandlerService $apiHandler,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \CheckoutCom\Magento2\Helper\Logger $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->apiHandler = $apiHandler;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Handles the controller method.
     */
    public function execute()
    {
       
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        // $response = ['success' => 'true'];
        $response = [];
        $resultJson->setData($response);        
        return $resultJson;       
    }
}
