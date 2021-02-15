<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\OrderOrigin\Plugin\Magento\Checkout\Model;

/**
 * Description of GuestPaymentInformationManagement
 *
 * @author nada
 */
class GuestPaymentInformationManagement {
    //put your code here
     public $request;
    
    
    
    public $order;
    
    public function __construct(
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Sales\Api\OrderRepositoryInterface $order) {
        $this->request = $request;
        $this->order = $order;
    }

    public function aftersavePaymentInformationAndPlaceOrder(
            \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
            $result,
            $cartId,
            $email,
            \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
            \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $deviceHeader = $this->request->getHeader('device');
        if($deviceHeader == null){
          $deviceHeader="No Device";  
        }
        $orderObject = $this->order->get($result);
        $orderObject->setOrderOrigin($deviceHeader);
        $orderObject->addStatusToHistory($orderObject->getStatus(),'Order Placed From Origin '.$deviceHeader);
        $this->order->save($orderObject);
        return $result;
    }

}
