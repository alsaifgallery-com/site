<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnecoms\Sms\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vnecoms\Sms\Model\Mobile;
use Magento\Framework\Exception\LocalizedException;


class OTPCodeManagement implements \Vnecoms\Sms\Api\OTPCodeManagementInterface
{

    public function __construct(
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        DateTime $date,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->date = $date;
        $this->filter = $filter;
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getOTPCode($mobileNum)
    {
      $response = new \Magento\Framework\DataObject();
      try{
          // $mobileNum = $this->getRequest()->getParam('mobileNum');
          $customerId = $this->customerSession->getCustomerId();
          $otpGenerator = $this->_objectManager->create('Vnecoms\Sms\Model\Otp\Generator');
          $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');

          /* Save the mobile number*/
          $mobile = $this->_objectManager->create('Vnecoms\Sms\Model\Mobile');

          if($helper->isUniqueMobileNumber()){
              /* Check if the mobile is used by other */
              $collection = $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection()
                  ->addAttributeToFilter('mobilenumber', $mobileNum)
                  ->addAttributeToFilter('entity_id', ['neq' => $this->customerSession->getId()]);

              if($collection->count()){
                  throw new LocalizedException(__("The mobile number is used by another customer account."));
              }
          }

          if($customerId){
              if($mobileNum == $this->customerSession->getCustomer()->getMobilenumber()){
                  throw new LocalizedException(__("You are using this mobile number already."));
              }

              $mobile->load($customerId, 'customer_id');
          }else{
              $mobileCollection = $mobile->getCollection()
                  ->addFieldToFilter('mobile', $mobileNum)
                  ->addFieldToFilter('status', Mobile::STATUS_NOT_VERIFIED)
                  ->addFieldToFilter('customer_id', ['null' => true]);
              if($mobileCollection->count()){
                  $mobile = $mobileCollection->getFirstItem();
              }
          }

          if(
              $mobile->getMobileId() &&
              !$mobile->isExpiredOTP()
          ) {
              $otp = $mobile->getOtp();
          }else{
              $otp = $otpGenerator->generateCode();
          }
          $mobile->addData([
              'customer_id' => $customerId?$customerId:null,
              'mobile' => $mobileNum,
              'otp' => $otp,
              'otp_created_at' => $this->date->timestamp(),
              'status' => 0,
          ])->save();

          /* Send otp Message*/
          $message = $helper->getOtpMessage();
          $this->filter->setVariables(['otp_code' => $otp]);
          $message = $this->filter->filter($message);
          $helper->sendSms($mobileNum, $message);

          $data = [
              'code' => $otp
          ];
      }catch(\Exception $e){
          $data = [
              'success' => "false",
              'msg' => $e->getMessage(),
          ];
      }

      return $data;
    }
}
