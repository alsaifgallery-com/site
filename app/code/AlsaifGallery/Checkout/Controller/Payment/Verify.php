<?php


namespace AlsaifGallery\Checkout\Controller\Payment;

use \Checkout\Models\Payments\Refund;
use \Checkout\Models\Payments\Voids;

/**
 * Class Verify
 */
class Verify extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var CheckoutApi
     */
    public $apiHandler;

    /**
     * @var QuoteHandlerService
     */
    public $quoteHandler;

    /**
     * @var OrderHandlerService
     */
    public $orderHandler;

    /**
     * @var Utilities
     */
    public $utilities;

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
        \CheckoutCom\Magento2\Gateway\Config\Config $config,
        \CheckoutCom\Magento2\Model\Service\ApiHandlerService $apiHandler,
        \CheckoutCom\Magento2\Model\Service\QuoteHandlerService $quoteHandler,
        \CheckoutCom\Magento2\Model\Service\OrderHandlerService $orderHandler,
        \CheckoutCom\Magento2\Helper\Utilities $utilities,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \CheckoutCom\Magento2\Helper\Logger $logger
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->apiHandler = $apiHandler;
        $this->quoteHandler = $quoteHandler;
        $this->orderHandler = $orderHandler;
        $this->utilities = $utilities;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * Handles the controller method.
     */
    private function executeOld()
    {
        // die("vvvvvvvvvvv");
        
        try {
            // Try to load a quote
            $this->quote = $this->quoteHandler->getQuote();

            // Get the session id
            $sessionId = $this->getRequest()->getParam('cko-session-id', null);
            if ($sessionId) {
                // Initialize the API handler
                $api = $this->apiHandler->init();

                // Get the payment details
                $response = $api->getPaymentDetails($sessionId);

                // Set the method ID
                $this->methodId = $response->metadata['methodId'];

                // Logging
                $this->logger->display($response);
                
                // Process the response
                if ($api->isValidResponse($response)) {
                    if (!$this->placeOrder($response)) {
                        // Add and error message
                        $this->messageManager->addErrorMessage(
                            __('The transaction could not be processed or has been cancelled.')
                        );

                        // Return to the cart
                        return $this->_redirect('checkout/cart', ['_secure' => true]);
                    }
                    
                    return $this->_redirect('checkout/onepage/success', ['_secure' => true]);
                }
            }
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
        }
    }
    /**
     * Handles the controller method.
     */
    public function execute()
    {
    
        try {
            // Try to load a quote
            // $this->quote = $this->quoteHandler->getQuote();

            // Get the session id
            $sessionId = $this->getRequest()->getParam('cko-session-id', null);
            if ($sessionId) {
                // Initialize the API handler
                $api = $this->apiHandler->init();

                // Get the payment details
                $response = $api->getPaymentDetails($sessionId);

                // var_dump($response);die;
                
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
                
                // echo ( $sessionId );die;
                // echo ( "" );die; //done
                return $this->_redirect('alsaifgallery_checkout/payment/Complete', ['_secure' => true]);

                
                // Process the response
//                if ($api->isValidResponse($response)) {
//                    if (!$this->placeOrder($response)) {
//                        // Add and error message
//                        $this->messageManager->addErrorMessage(
//                            __('The transaction could not be processed or has been cancelled.')
//                        );
//
//                        // Return to the cart
//                        return $this->_redirect('checkout/cart', ['_secure' => true]);
//                    }
//                    
//                    return $this->_redirect('checkout/onepage/success', ['_secure' => true]);
//                }
            }
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
            return $this->_redirect('alsaifgallery_checkout/payment/Complete', ['_secure' => true]);
        }
    }

    /**
     * Handles the order placing process.
     *
     * @param array $response The response
     *
     * @return mixed
     */
    public function placeOrder($response = null)
    {
        try {
            // Initialize the API handler
            $api = $this->apiHandler->init();

            // Get the reserved order increment id
            $reservedIncrementId = $this->quoteHandler
                ->getReference($this->quote);

            // Get the payment details
            $paymentDetails = $api->getPaymentDetails($response->id);

            // Prepare the quote filters
            $filters = $this->quoteHandler->prepareQuoteFilters(
                $paymentDetails,
                $reservedIncrementId
            );

            // Create an order
            $order = $this->orderHandler
                ->setMethodId($this->methodId)
                ->handleOrder(
                    $response,
                    $filters
                );

            // Add the payment info to the order
            $order = $this->utilities
                ->setPaymentData($order, $response);

            // Save the order
            $order->save();

            // Check if the order is valid
            if (!$this->orderHandler->isOrder($order)) {
                $this->cancelPayment($response);
                return null;
            }

            return $order;
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
            return null;
        }
    }

    /**
     * Prepares the quote filters.
     *
     * @param array $paymentDetails
     * @param string $reservedIncrementId
     *
     * @return array
     */
    public function prepareQuoteFilters($paymentDetails, $reservedIncrementId)
    {
        // Prepare the filters array
        $filters = ['increment_id' => $reservedIncrementId];

        // Retrieve the quote metadata
        $quoteData = isset($paymentDetails->metadata['quoteData'])
        && !empty($paymentDetails->metadata['quoteData'])
        ? json_decode($paymentDetails->metadata['quoteData'], true)
        : [];

        return array_merge($filters, $quoteData);
    }

    /**
     * Cancels a payment.
     *
     * @param array $response The response
     *
     * @return void
     */
    public function cancelPayment($response)
    {
        try {
            // Initialize the API handler
            $api = $this->apiHandler->init();

            // Refund or void accordingly
            if ($this->config->needsAutoCapture($this->methodId)) {
                // Refund
                $api->checkoutApi->payments()->refund(new Refund($response->getId()));
            } else {
                // Void
                $api->checkoutApi->payments()->void(new Voids($response->getId()));
            }
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
        }
    }
}
