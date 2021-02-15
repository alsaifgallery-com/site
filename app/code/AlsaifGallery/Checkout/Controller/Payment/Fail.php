<?php

namespace AlsaifGallery\Checkout\Controller\Payment;

/**
 * Class Fail
 */
class Fail extends \Magento\Framework\App\Action\Action
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

    /**
     * PlaceOrder constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \CheckoutCom\Magento2\Model\Service\ApiHandlerService $apiHandler,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \CheckoutCom\Magento2\Helper\Logger $logger
    ) {
        parent::__construct($context);

        $this->apiHandler = $apiHandler;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Handles the controller method.
     */
    public function execute()
    {
         try {
            // Get the session id
            $sessionId = $this->getRequest()->getParam('cko-session-id', null);
            if ($sessionId) {
                // Initialize the API handler
                $api = $this->apiHandler->init();

                // Get the payment details
                $response = $api->getPaymentDetails($sessionId);

                
                // Set the method ID
                $this->methodId = $response->metadata['methodId'];
                
                //save data
                $cartId = intval(str_replace("ref_qoute_","", $response->reference) );
                $quote = $this->quoteRepository->get($cartId);
                 // var_dump($response->reference ,$cartId ,  $sessionId ,$response->source['id']  );
                if($quote->getId()){
                    $qoutePay = $quote->getPayment();

                    if( isset( $sessionId )){
                        $qoutePay->setCheckoutcomSessionId( $sessionId );
                    }
                    
                    if( isset( $response )){
                        $qoutePay->setCheckoutcomThreedsData( json_encode( $response ) );
                    }
                    if( isset( $response->source['id'] )){
                        $qoutePay->setCheckoutcomSourceId( $response->source['id'] );
                    }
                    
                    $qoutePay->save();
                    
                }
                
                // Logging
                $this->logger->display($response);
                
                // Display the message
//                $this->messageManager->addErrorMessage(__('The transaction could not be processed.'));
                // echo(__('The transaction could not be processed.'));
                // echo("");//done
                return $this->_redirect('alsaifgallery_checkout/payment/Complete', ['_secure' => true]);
            }
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
            return $this->_redirect('alsaifgallery_checkout/payment/Complete', ['_secure' => true]);
        } finally {
            // Return to the cart
            //return $this->_redirect('checkout/cart', ['_secure' => true]);
        }
    }
}
