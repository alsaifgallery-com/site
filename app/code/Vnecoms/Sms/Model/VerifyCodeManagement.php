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

class VerifyCodeManagement implements \Vnecoms\Sms\Api\VerifyCodeManagementInterface
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
    public function getVerifyCode($mobileNum, $code)
    {
      $response = new \Magento\Framework\DataObject();
      try{
          $mobileNum = $mobileNum;
          $otp = $code;
          $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');
          if($helper->isUniqueMobileNumber()){
              /* Check if the mobile is used by other */
              $collection = $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection()
                  ->addAttributeToFilter('mobilenumber', $mobileNum)
                  ->addAttributeToFilter('entity_id', ['neq' => $this->customerSession->getId()]);

              if($collection->count()){
                  throw new \Magento\Framework\Exception\LocalizedException(__("The mobile number is used by another customer account."));
              }
          }

          /* Save the mobile number*/
          $collection = $this->_objectManager->create('Vnecoms\Sms\Model\ResourceModel\Mobile\Collection')
              ->addFieldToFilter('mobile', $mobileNum)
              ->addFieldToFilter('otp', $otp);

          if(!$collection->count()){
              // throw new \Exception(__("The OTP code is not valid."));
              $data = [
                  'success' => false,
                  'msg' => "The OTP code is not valid.",
              ];
              return $data;
          }

          $mobile = $collection->getFirstItem();

          $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');

          if((strtotime($mobile->getOtpCreatedAt()) + $helper->getOtpExpiredPeriodTime()) < $this->date->timestamp()){
              // throw new \Exception(__("The OTP code is expired."));
              $data = [
                  'success' => false,
                  'msg' => "The OTP code is expired.",
              ];
              return $data;
          }

          $mobile->setStatus(Mobile::STATUS_VERIFIED)->save();
          $this->customerSession->setOtpResendCount(0);
          $data = [
              'success' => true,
              'otp' => $otp,
          ];
      }catch(\Exception $e){
          $data = [
              'success' => false,
              'msg' => $e->getMessage(),
          ];
      }


      // $response->setData($data);
      return $data;// $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
