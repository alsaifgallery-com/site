<?php

namespace AlsaifGallery\Checkout\Model;

use \Checkout\Models\Payments\Refund;
use \Checkout\Models\Payments\Voids;
use \Checkout\Models\Payments\Metadata;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;

class PaymentInformationManagement implements \AlsaifGallery\Checkout\Api\PaymentInformationManagementInterface {

    protected $paymentInformationManagement;
    protected $orderItemRepo;
    protected $orderRepo;
    protected $checkoutHelper;
    protected $cartManagment;
    protected $checkoutPayment;
    protected $quoteRepository;
    protected $transaction;
    protected $invoiceService;
    protected $orderNotifier;
    protected $transactionBuilder;
    
    
    
    /**
     * @var QuoteHandlerService
     */
    public $quoteHandler;

    /**
     * @var OrderHandlerService
     */
    public $orderHandler;

    /**
     * @var MethodHandlerService
     */
    public $methodHandler;

    /**
     * @var ApiHandlerService
     */
    public $apiHandler;

    /**
     * @var Utilities
     */
    public $utilities;

    /**
     * @var Logger
     */
    public $logger;
    
    protected $config;
    
    protected $methodId;
    protected $cartTotals;
    protected $storeManager;




    // /home/helal/server/balleh.me/vendor/checkoutcom/magento2/Controller/Button/PlaceOrder.php
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement, 
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepo,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepo,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepo, 
        \AlsaifGallery\Checkout\Helper\Data $checkoutHelper,
        \Magento\Quote\Api\CartManagementInterface $cartManagment,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \AlsaifGallery\Checkout\Model\CardPaymentMethod $checkoutPayment,
        \Magento\Sales\Model\OrderNotifier $orderNotifier,
        \Magento\Sales\Model\Order\Payment\Transaction\Builder $transactionBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
       \CheckoutCom\Magento2\Model\Service\QuoteHandlerService $quoteHandler,
        \CheckoutCom\Magento2\Model\Service\OrderHandlerService $orderHandler,
        \CheckoutCom\Magento2\Model\Service\MethodHandlerService $methodHandler,
        \CheckoutCom\Magento2\Model\Service\ApiHandlerService $apiHandler,
        \CheckoutCom\Magento2\Helper\Utilities $utilities,
        \CheckoutCom\Magento2\Helper\Logger $logger,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotals,
        \CheckoutCom\Magento2\Gateway\Config\Config  $config
    ) {
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->orderItemRepo = $orderItemRepo;
        $this->orderRepo = $orderRepo;
        $this->productRepo = $productRepo;
        $this->checkoutHelper = $checkoutHelper;
        $this->cartManagment = $cartManagment;
        $this->checkoutPayment = $checkoutPayment;
        $this->quoteRepository = $quoteRepository;


        $this->transactionBuilder = $transactionBuilder;
        $this->orderNotifier = $orderNotifier;

        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        
        
        $this->quoteHandler = $quoteHandler;
        $this->orderHandler = $orderHandler;
        $this->methodHandler = $methodHandler;
        $this->apiHandler = $apiHandler;
        $this->utilities = $utilities;
        $this->logger = $logger;
        $this->config = $config;
        
        $this->cartTotals = $cartTotals;
        $this->storeManager= $storeManager;
        
    }

    /**
     * {@inheritdoc}
     */
      public function savePaymentInformation(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ){
      $ret =  $this->paymentInformationManagement->savePaymentInformation($cartId, $paymentMethod, $billingAddress);  
      $totals = $this->cartTotals->get($cartId);
      return $totals;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function savePaymentInformationAndPlaceOrder(
    $cartId, \Magento\Quote\Api\Data\PaymentInterface $paymentMethod, \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $storeId = $this->storeManager->getStore()->getId();
        $response = null;
        $is3DFailed = false;
        $is3Ds = false;
        $paymentStatus = false;
        $creditToken = $this->checkoutHelper->getCreditToken();
        $quote = $this->quoteRepository->get($cartId);
        // $methodId = "checkoutcom_card_payment";
        $methodId =  $paymentMethod->getMethod() ;
        $checkoutPaymentCodes =  ["checkoutcom_card_payment"];
        $qoutePay = $quote->getPayment();
        $qoutePayAddtionalData = $qoutePay->getCheckoutcomData();
        if(is_null($qoutePayAddtionalData)){
            $qoutePayAddtionalData = json_decode("{}");
        }else{
            $qoutePayAddtionalData = json_decode($qoutePay->getCheckoutcomData());
        }
        $response3DsTmp = $qoutePay->getCheckoutcomThreedsData() ;
        if( !is_null( $response3DsTmp )){
            $is3Ds = true;
            $response = json_decode($response3DsTmp);
            // var_dump($response);
            $qoutePayAddtionalData->payment_id =  $response->id;
            $qoutePayAddtionalData->payment_status =  $response->status;
            $qoutePayAddtionalData->payment_approved =  $response->approved;
            if( $qoutePayAddtionalData->payment_approved  == false ){
                $is3DFailed = true;
            }
            // $qoutePayAddtionalData->credit_token =   $creditToken ;
            if( !isset($qoutePayAddtionalData->credit_token)){
               $qoutePayAddtionalData->credit_token=""; 
            }
            $qoutePayAddtionalData->http_code =  $response->http_code;
            $qoutePayAddtionalData->checkoutcom_source_id  =   $qoutePay->getCheckoutcomSourceId();
            $qoutePayAddtionalData->checkoutcom_session_id =  $qoutePay->getCheckoutcomSessionId();
            $qoutePayAddtionalData->response =  $response ;
            // var_dump($qoutePayAddtionalData);
            $qoutePay->setCheckoutcomData(json_encode( $qoutePayAddtionalData ));
            // $qoutePay->save();
        } 
        $qoutePay->setMethod( $paymentMethod->getMethod() );
        $qoutePay->save();
        
        if ($creditToken != false) {
            if (!in_array($paymentMethod->getMethod(), $checkoutPaymentCodes )) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __(
                        "The paymentmethod not correct."
                )
                );
        }
            
        if (in_array($paymentMethod->getMethod(), $checkoutPaymentCodes)) {
            if( isset( $qoutePayAddtionalData->payment_approved )){
                $paymentApproved = $qoutePayAddtionalData->payment_approved;
                switch ($paymentApproved){
                    case true:
                        $paymentStatus = true;
                        if( isset( $qoutePayAddtionalData->credit_token)){
                            if( $creditToken == $qoutePayAddtionalData->credit_token){
                                // do order
                                $paymentStatus = true;
                                if( isset( $qoutePayAddtionalData->response)){
                                    $response =  $qoutePayAddtionalData->response;    
                                }
                            }else if ( $creditToken != $qoutePayAddtionalData->credit_token){
                                // order bayed but new token given
                                // make sure from gateway
                                // ?? do order
                               throw new \Magento\Framework\Exception\LocalizedException(
                                    __(
                                        "Credit token is not the same as the one used to pay"
                                    )
                                );
                            }
                        }else{
                            // error token not saved ??
                               throw new \Magento\Framework\Exception\LocalizedException(
                                    __(
                                        "Credit token is not saved and cart say it paid ?!!!"
                                    )
                                );
                        }
                        break;
                    case false:
                        // pay
                        // order
                        $paymentStatus = false;
                        break;
                    default :
                        //ereeor
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __(
                                "No vaild data try agian."
                            )
                         );
                        break;
                }
            }
        }
        
           
        
            $data = [];
//            $testAmount = 11105;
//            $testAmount = 11112;
//            $testAmount = 11114;
            // $testAmount = 25500;
//            var_dump( $paymentStatus ,$is3Ds, $is3DFailed , $creditToken , $qoutePayAddtionalData->credit_token);
            // var_dump( $paymentStatus ,$is3Ds, $is3DFailed , $creditToken , $qoutePayAddtionalData);
            $testAmount = 0; 
            if( $paymentStatus == false){
                if( $is3Ds && ($is3DFailed == true) ){
                    // do nothing by pass to show status
                    // if token differ from saved do payment another time ---> user retry
                    if ( $creditToken != $qoutePayAddtionalData->credit_token){
                        $response = $this ->checkoutPayment
                                      ->sendPaymentRequestMobile($cartId, $creditToken, $data,"",$testAmount);
                    }
                }else{
                    $response = $this ->checkoutPayment
                                  ->sendPaymentRequestMobile($cartId, $creditToken, $data,"",$testAmount);
                }
            }

//            print_r($response->http_code);die;
             // print_r($response);die;
            if (isset($response->http_code) && ($response->http_code == 200 )) {
                // 200  Payment processed successfully By 3Ds
                if( $is3Ds == true ){
                    if ($response->approved == true) {
                        // data saved already line 150
    //                    $paymentStatus = true;
    //                    $qoutePayAddtionalData->payment_id =  $response->id;
    //                    $qoutePayAddtionalData->payment_status =  $response->status;
    //                    $qoutePayAddtionalData->payment_approved =  $response->approved;
    //                    $qoutePayAddtionalData->credit_token =   $creditToken ;
    //                    $qoutePayAddtionalData->http_code =  $response->http_code;
    //                    $qoutePayAddtionalData->response =  $response ;
    //                    $qoutePay->setCheckoutcomData(json_encode( $qoutePayAddtionalData ));
    //                    $qoutePay->save();
                    } else {
                        // print_r($response);
                        // die();
                        //throw error
                        $resArr["status"] = false;
                        $resArr["message"] = __("Payment status is : %1, please try again.",$response->status)->render();
                        $resArr["payment_id"] =  $response->id;
                        $resArr["payment_3Ds"] = $is3Ds;
                        $resArr["payment_status"] =  $response->status;
                        $resArr["payment_approved"] =  $response->approved;
                        $resArr["http_code"] =  $response->http_code;

    //                    $qoutePayAddtionalData->payment_id =  $response->id;
    //                    $qoutePayAddtionalData->payment_status =  $response->status;
    //                    $qoutePayAddtionalData->payment_approved =  $response->approved;
    //                    $qoutePayAddtionalData->credit_token =   $creditToken ;
    //                    $qoutePayAddtionalData->http_code =  $response->http_code;
    //                    $qoutePayAddtionalData->response =  $response ;
    //                    $qoutePay->setCheckoutcomData( json_encode($qoutePayAddtionalData ));
    //                    $qoutePay->save();

                        return [$resArr];
                    }
                }// if( $is3Ds == true )
                
            }
            if (isset($response->http_code) && ($response->http_code == 201 )) {
                // 201  Payment processed successfully
                if ($response->approved == true) {
                    $paymentStatus = true;
                    $qoutePayAddtionalData->payment_id =  $response->id;
                    $qoutePayAddtionalData->payment_status =  $response->status;
                    $qoutePayAddtionalData->payment_approved =  $response->approved;
                    $qoutePayAddtionalData->credit_token =   $creditToken ;
                    $qoutePayAddtionalData->http_code =  $response->http_code;
                    $qoutePayAddtionalData->response =  $response ;
                    $qoutePay->setCheckoutcomData(json_encode( $qoutePayAddtionalData ));
                    $qoutePay->save();
                } else {
                    // print_r($response);
                    // die();
                    //throw error
                    $resArr["status"] = false;
                    $resArr["message"] = __("Payment status is : %1, please try again.",$response->status)->render();
                    $resArr["payment_id"] =  $response->id;
                    $resArr["payment_3Ds"] = $is3Ds;
                    $resArr["payment_status"] =  $response->status;
                    $resArr["payment_approved"] =  $response->approved;
                    $resArr["http_code"] =  $response->http_code;
                    
                    $qoutePayAddtionalData->payment_id =  $response->id;
                    $qoutePayAddtionalData->payment_status =  $response->status;
                    $qoutePayAddtionalData->payment_approved =  $response->approved;
                    $qoutePayAddtionalData->credit_token =   $creditToken ;
                    $qoutePayAddtionalData->http_code =  $response->http_code;
                    $qoutePayAddtionalData->response =  $response ;
                    $qoutePay->setCheckoutcomData( json_encode($qoutePayAddtionalData ));
                    $qoutePay->save();
                    
                    return [$resArr];
                }
                
            }
            if (isset($response->http_code) && ($response->http_code == 202 )) {
                // 202  Payment asynchronous or further action required
                // die( " Payment asynchronous or further action required " );
                $resArr["status"] = false;
                $resArr["message"] = __("Payment asynchronous or further action required.")->render();
                if (isset($response->_links['redirect'])) {
                    $is3Ds = true;
                    $resArr["payment_3Ds"] = $is3Ds;
                    $resArr["payment_id"] =  $response->id;
                    $resArr["payment_status"] =  $response->status;
                    $resArr["redirect"] = $response->_links['redirect']['href'];
                }
                // var_dump($response);
                // how we will handle it after url ???
                $qoutePayAddtionalData->payment_id =  $response->id;
                $qoutePayAddtionalData->payment_status =  $response->status;
                // $qoutePayAddtionalData->payment_approved =  $response->approved;
                $qoutePayAddtionalData->credit_token =   $creditToken ;
                $qoutePayAddtionalData->http_code =  $response->http_code;
                $qoutePayAddtionalData->response =  $response ;
                $qoutePay->setCheckoutcomData( json_encode($qoutePayAddtionalData ));
                $qoutePay->save();
                
                return [$resArr];
            }
            if (isset($response->http_code) && ($response->http_code == 401 )) {
                // 401  Unauthorized 
                     print_r($response);
                     die();
            }
            if (isset($response->http_code) && ($response->http_code == 422 )) {
                // 422 Invalid data was sent 
                     print_r($response);
                     die();
            }
            if (isset($response->http_code) && ($response->http_code == 429 )) {
                // 429  Too many requests or duplicate request detected 
                     print_r($response);
                     die();
            }
            if (isset($response->http_code) && ($response->http_code == 502 )) {
                // 502 Bad gateway 
                     print_r($response);
                     die();
            }
        }
        try{
            $res = $this->paymentInformationManagement->savePaymentInformationAndPlaceOrder($cartId, $paymentMethod, $billingAddress);
        }catch( \Exception $e){
            // die("999999");
            // $this->cancelPayment($response);
            throw $e ;
        }

        $validator = new \Zend\Validator\Digits();
        if ($validator->isValid($res)) {
            $order = $this->orderRepo->get($res);
            $resArr["status"] = true;
            $resArr["message"] = __("Placing Order successed.")->render();
            $resArr["new_qoute_id"] = $this->cartManagment->createEmptyCartForCustomer($order->getCustomerId());
            $resArr["order_id"] = $res;
            if( isset( $response )){
                $resArr["payment_id"] =  $response->id;
                $resArr["payment_status"] =  $response->status;
                $resArr["payment_approved"] =  $response->approved;
                $resArr["payment_3Ds"] = $is3Ds;
            }
            if ($order->getId()) {
                $resArr["incremental_id"] = $order->getIncrementId();
                $resArr["items"] = [];
                foreach ($order->getItems() as $item) {
                    $product = $this->productRepo->getById($item->getProductId(),false,$storeId);
                    $productExt = $product->getExtensionAttributes();
                    $itemData = [];
                    $itemId = $item->getItemId();
                    $itemData['item_id'] = $itemId;
                    $itemData['name'] = $item->getName();
                    $itemData['qty'] = $item->getQtyOrdered();
                    $itemData['price'] = $item->getPrice();
                    if (!is_null($productExt)) {
                        $itemData['thumbnail'] = $productExt->getThumbnail();
                        $itemData['url_base'] = $productExt->getUrlBase();
                        $itemData['brand'] = $productExt->getBrand();
                        $itemData['brand_image'] = $productExt->getBrandImage();
                        $itemData['brand_option_id'] = $productExt->getBrandOptionId();
                    }
                    $resArr["items"][] = $itemData;
                }
                $resArr["total_paid"] = $order->getGrandTotal();
                // create transction
                // craete invoice
                if ($paymentStatus == true) {
                    $message = 'checkout.com Payment Details:<br/>';

                    if (isset($response->http_code)) {
                        $message .= 'http_code #: ' . $response->http_code . "<br/>";
                    }

                    if (isset($response->id)) {
                        $message .= 'Payment id: ' . $response->id . "<br/>";
                    }

                    if (isset($response->action_id)) {
                        $message .= 'Action id: ' . $response->action_id . "<br/>";
                    }

                    if (isset($response->processing)) {
                        if (is_array($response->processing )){
                            if (isset($response->processing['acquirer_transaction_id'])) {
                                $message .= 'Acquirer transaction id: ' . $response->processing['acquirer_transaction_id'] . "<br/>";
                            }
                            if (isset($response->processing['retrieval_reference_number'])) {
                                $message .= 'Retrieval reference number: ' . $response->processing['retrieval_reference_number'] . "<br/>";
                            }
                        }else{
                            if (isset($response->processing->acquirer_transaction_id)) {
                                $message .= 'Acquirer transaction id: ' . $response->processing->acquirer_transaction_id . "<br/>";
                            }
                            if (isset($response->processing->retrieval_reference_number)) {
                                $message .= 'Retrieval reference number: ' . $response->processing->retrieval_reference_number . "<br/>";
                            }                        
                        }
                    }

                    if (isset($response->amount)) {
                        $message .= 'Amount: ' . ($response->amount / 100) . "<br/>";
                    }

                    if (isset($response->currency)) {
                        $message .= 'Currency: ' . $response->currency . "<br/>";
                    }

                    if (isset($response->reference)) {
                        $message .= 'Reference: ' . $response->reference . "<br/>";
                    }

                    if (isset($response->processed_on)) {
                        $message .= 'Processed on: ' . $response->processed_on . "<br/>";
                    }

                    if (isset($response->response_summary)) {
                        $message .= 'Response summary: ' . $response->response_summary . "<br/>";
                    }
                    if (isset($response->response_code)) {
                        $message .= 'Response code: ' . $response->response_code . "<br/>";
                    }
                    if (isset($response->status)) {
                        $message .= 'Status: ' . $response->status . "<br/>";
                    }
                    if (isset($response->approved)) {
                        $message .= 'Approved: ' . $response->approved . "<br/>";
                    }
                    if (isset($response->source)) {
                        if (is_array($response->source )){
                            if (isset($response->source['id'])) {
                                $message .= 'Source id: ' . $response->source['id'] . "<br/>";
                            }
                            if (isset($response->source['type'])) {
                                $message .= 'Source type: ' . $response->source['type'] . "<br/>";
                            }
                            if (isset($response->source['scheme'])) {
                                $message .= 'Source scheme: ' . $response->source['scheme'] . "<br/>";
                            }
                            if (isset($response->source['card_type'])) {
                                $message .= 'Source card type: ' . $response->source['card_type'] . "<br/>";
                            }
                            if (isset($response->source['last4'])) {
                                $message .= 'Card: *****' . $response->source['last4'] . "<br/>";
                            }
                        }else{
                            if (isset($response->source->id)) {
                                $message .= 'Source id: ' . $response->source->id . "<br/>";
                            }
                            if (isset($response->source->type)) {
                                $message .= 'Source type: ' . $response->source->type . "<br/>";
                            }
                            if (isset($response->source->scheme)) {
                                $message .= 'Source scheme: ' . $response->source->scheme . "<br/>";
                            }
                            if (isset($response->source->card_type)) {
                                $message .= 'Source card type: ' . $response->source->card_type . "<br/>";
                            }
                            if (isset($response->source->last4)) {
                                $message .= 'Card: *****' . $response->source->last4 . "<br/>";
                            } 
                        }
                    }


                    if (!is_null($order) && $order->getId()) {

    //                $amount = round($order->getGrandTotal(), 3);
                        $payment = $order->getPayment();
                        if (isset($response->id)) {
                            $TransactionId = $response->id;
                        }else{
                           $TransactionId = uniqid(); 
                        }
//                        if( isset($response->processing)){
//                            if(is_array(  $response->processing )){
//                                $TransactionId = $response->processing['acquirer_transaction_id'];
//                            }else{
//                                $TransactionId = $response->processing->acquirer_transaction_id;
//                            }
//                        }else{
//                            $TransactionId = $response->id;
//                        }
                        $payment->setTransactionId($TransactionId);
                        $payment->setLastTransId($TransactionId);
                        if (isset($response->http_code)) {
                            $payment->setAdditionalInformation('http_code #: ', $response->http_code);
                        }
                        if (isset($response->id)) {
                            $payment->setAdditionalInformation('Payment id: ', $response->id);
                        }
                        if ( isset( $response->action_id )) {
                            $payment->setAdditionalInformation('Action id: ', $response->action_id);
                        }
                        $payment->setAdditionalInformation('Acquirer transaction id: ', $TransactionId);
                        if( isset($response->processing)){
                            if(is_array(  $response->processing )){
                                $payment->setAdditionalInformation('Retrieval reference number: ', $response->processing['retrieval_reference_number']);
                            }else{
                                $payment->setAdditionalInformation('Retrieval reference number: ', $response->processing->retrieval_reference_number);
                            }
                        }
                        if ( isset($response->amount)) {
                            $payment->setAdditionalInformation('Amount: ', ($response->amount / 100));
                        }
                        if ( isset($response->currency)) {
                            $payment->setAdditionalInformation('Currency: ', $response->currency);
                        }
                        if ( isset($response->reference)) {
                            $payment->setAdditionalInformation('Reference: ', $response->reference);
                        }
                        if ( isset(  $response->processed_on )) {
                            $payment->setAdditionalInformation('Processed on: ', $response->processed_on);
                        }
                        if ( isset( $response->response_summary )) {
                            $payment->setAdditionalInformation('Response summary: ', $response->response_summary);
                        }
                        if ( isset( $response->response_code )) {
                            $payment->setAdditionalInformation('Response code: ', $response->response_code);
                        }
                        if ( isset($response->status)) {
                            $payment->setAdditionalInformation('Status: ', $response->status);
                        }
                        if ( isset( $response->approved )) {
                            $payment->setAdditionalInformation('Approved: ', $response->approved);
                        }
                        if ( isset( $response->approved )) {
                            if(is_array(  $response->source )){
                                if (isset($response->source['id'])) {
                                    $payment->setAdditionalInformation('Source id: ', $response->source['id']);
                                }
                                if (isset($response->source['type'])) {
                                    $payment->setAdditionalInformation('Source type: ', $response->source['type']);
                                }
                                if (isset($response->source['scheme'])) {
                                    $payment->setAdditionalInformation('Source scheme: ', $response->source['scheme']);
                                }
                                if (isset( $response->source['card_type'])) {
                                    $payment->setAdditionalInformation('Source card type: ', $response->source['card_type']);
                                }
                                if (isset( $response->source['last4'])) {
                                    $payment->setAdditionalInformation('Card:', "********" . $response->source['last4']);
                                }
                            }else{
                                if (isset($response->source->id)) {
                                    $payment->setAdditionalInformation('Source id: ', $response->source->id);
                                }
                                if (isset($response->source->type)) {
                                    $payment->setAdditionalInformation('Source type: ', $response->source->type);
                                }
                                if (isset( $response->source->scheme)) {
                                    $payment->setAdditionalInformation('Source scheme: ', $response->source->scheme);
                                }
                                if (isset($response->source->card_type)) {
                                    $payment->setAdditionalInformation('Source card type: ', $response->source->card_type);
                                }
                                if (isset($response->source->last4)) {
                                    $payment->setAdditionalInformation('Card:', "********" . $response->source->last4);
                                }
                            }
                        }

                        $payment->setAdditionalInformation((array) $payment->getAdditionalInformation());

                        //----
                        $trans = $this->transactionBuilder;

                        $transaction = $trans->setPayment($payment)
                                ->setOrder($order)
                                ->setTransactionId($TransactionId)
                                ->setAdditionalInformation((array) $payment->getAdditionalInformation())
                                ->setFailSafe(true)
                                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

                        $payment->addTransactionCommentsToOrder($transaction, $message);
                        $payment->setParentTransactionId(null);

                        $payment->save();

                        $this->orderNotifier->notify($order);

                        $order->addStatusHistoryComment(__('Transaction is approved by the bank'), \Magento\Sales\Model\Order::STATE_PROCESSING)->setIsCustomerNotified(true);
                        // $order->addStatusHistoryComment(__('Transaction is approved by the bank'), "knetPaid")->setIsCustomerNotified(true);

                        // >>>
                        // Add the payment info to the order
                        $order = $this->utilities->setPaymentData($order, $response);

                        $order->save();

                        $transaction->save();

                        if ($order->canInvoice()) {
                            $invoice = $this->invoiceService->prepareInvoice($order);
                            $invoice->setRequestedCaptureCase( \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                            $invoice->register();
                            $invoice->save();
                            $transactionSave = $this->transaction->addObject(
                                            $invoice
                                    )->addObject(
                                    $invoice->getOrder()
                            );
                            $transactionSave->save();
                            // $this->invoiceSender->send($invoice);
                            //send notification code
                            $order->addStatusHistoryComment(
                                            __('Notified customer about invoice #%1.', $invoice->getId())
                                    )
                                    // ->setIsCustomerNotified(true)
                                    ->save();
                        }
                    } 
                }// checkout end 
            } else {
                $resArr["status"] = false;
                $resArr["order_id"] = 0;
                $resArr["message"] = __("Placing Order failed.")->render();
            }

            //
            return [$resArr];
        }
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
            // var_dump( $this->config->needsAutoCapture($this->methodId) );die;
            // var_dump($response->id,$response->amount,$response->reference );die;
            // Refund or void accordingly
            if ($this->config->needsAutoCapture($this->methodId)) {
                // Refund
               // $api->checkoutApi->payments()->refund(new Refund($response->id,$response->amount,$response->reference ));
               $ref = new Refund($response->id,$response->amount,$response->reference ,new Metadata );
               $api->checkoutApi->payments()->refund(  $ref  );
//               $response = $api->checkoutApi
//                ->payments()
//                ->request($request);
               
            } else {
                // Void
//                $api->checkoutApi->payments()->void(new Voids($response->getId()));
                $api->checkoutApi->payments()->void(new Voids( $response->id ));
            }
        } catch (\Exception $e) {
            $this->logger->write($e->getMessage());
        }
    }
    
}
