<?php

namespace AlsaifGallery\Checkout\Model;

use \Checkout\Models\Payments\Payment;
use \Checkout\Models\Payments\ThreeDs;
use \Checkout\Models\Payments\TokenSource;
use \Checkout\Models\Payments\BillingDescriptor;

/**
 * Class CardPaymentMethod
 */
class CardPaymentMethod extends \CheckoutCom\Magento2\Model\Methods\CardPaymentMethod
{

    
     public function getPaymentDetails( $sessionId )
    {
    
        try {
            if ($sessionId) {
                // Initialize the API handler
                $api = $this->apiHandler->init();

                // Get the payment details
                $response = $api->getPaymentDetails($sessionId);
                
                return $response;
                // Logging
                // $this->logger->display($response);
                
                // echo ( $sessionId );die;

                
               }
        } catch (\Exception $e) {
           //  $this->logger->write($e->getMessage());
            return "";
        }
    }
    
    
    /**
     * Send a charge request.
     */
//    public function sendPaymentRequestMobile($data, $amount, $currency, $reference = '')
    public function sendPaymentRequestMobile($cartId, $token , $data, $reference = '', $testAmount=0)
    {
//        try {
            // Initialize the API handler
            $api = $this->apiHandler->init();

            // Get the quote
            // $quote = $this->quoteHandler->getQuote();
            $quote = $this->quoteRepository->get($cartId);
            $quote->collectTotals();
            $amount = $quote->getGrandTotal() * 100 ;
            if(!empty($testAmount)){
                $amount = $testAmount;
            }
            $currency = $quote->getCurrency()->getQuoteCurrencyCode();
            if( empty( $reference )){
                $reference = 'ref_qoute_'.$quote->getId();
            }

//            var_dump($amount ,  $currency , $token );die;
           //  var_dump($amount ,$currency , $token , $reference);die;
//            var_dump("dddd");die;
            // Set the token source
            $tokenSource = new TokenSource($token);
            $tokenSource->billing_address = $api->createBillingAddress($quote);

            // Set the payment
            $request = new Payment(
                $tokenSource,
                $currency
            );

            // Prepare the metadata array
            $request->metadata = ['methodId' => $this->_code];

            // Prepare the capture date setting
            $captureDate = $this->config->getCaptureTime($this->_code);

            // Prepare the MADA setting
            $madaEnabled = $this->config->getValue('mada_enabled', $this->_code);

            // Prepare the save card setting
            $saveCardEnabled = $this->config->getValue('save_card_option', $this->_code);

            // Set the request parameters
            $request->capture = $this->config->needsAutoCapture($this->_code);
            $request->amount = $amount;
            $request->reference = $reference;
            // 3D
             $request->success_url = $this->config->getStoreUrl() . 'alsaifgallery_checkout/payment/verify';
             $request->failure_url = $this->config->getStoreUrl() . 'alsaifgallery_checkout/payment/fail';
             $request->threeDs = new ThreeDs($this->config->needs3ds($this->_code));
             $request->threeDs->attempt_n3d = (bool) $this->config->getValue('attempt_n3d', $this->_code);
            // 3D 
            $request->description = __('Payment request from %1', $this->config->getStoreName());
            $request->customer = $api->createCustomer($quote);
            $request->payment_type = 'Regular';
            $request->shipping = $api->createShippingAddress($quote);
            if ($captureDate) {
                $request->capture_on = $this->config->getCaptureTime();
            }

            // Mada BIN Check
            if (isset($data['cardBin'])
                && $this->cardHandler->isMadaBin($data['cardBin'])
                && $madaEnabled
            ) {
                $request->metadata['udf1'] = 'MADA';
            }

            // Save card check
            if (isset($data['saveCard'])
                && json_decode($data['saveCard']) === true
                && $saveCardEnabled
                && $this->customerSession->isLoggedIn()
            ) {
                $request->metadata['saveCard'] = 1;
                $request->metadata['customerId'] = $this->customerSession->getCustomer()->getId();
            }

            // Billing descriptor
            if ($this->config->needsDynamicDescriptor()) {
                $request->billing_descriptor = new BillingDescriptor(
                    $this->config->getValue('descriptor_name'),
                    $this->config->getValue('descriptor_city')
                );
            }

            // Add the quote metadata
            $request->metadata['quoteData'] = json_encode($this->quoteHandler->getQuoteRequestData($quote));

            // Add the base metadata
            $request->metadata = array_merge(
                $request->metadata,
                $this->apiHandler->getBaseMetadata()
            );

           //  var_dump( $request );die;
            // Send the charge request
            $response = $api->checkoutApi
                ->payments()
                ->request($request);

            // var_dump( $response );die;
            
            return $response;
//        } catch (\Exception $e) {
//            $this->ckoLogger->write($e->getBody());
//            return null;
//        }
    }

}
