<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\SocialLogin\Model;

use Magento\Customer\Api\Data\CustomerInterface;

class SocialTokenManagement implements \AlsaifGallery\SocialLogin\Api\SocialTokenManagementInterface
{

    public function __construct(
        \Magento\Customer\Model\Customer $customerInfo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenModelFactory,
        \AlsaifGallery\MSCE\Api\CreateCustomerManagementInterface $createCustomerManagementInterface
    ) {
        $this->customer = $customerInfo;
        $this->storeManager = $storeManager;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->createCustomerManagementInterface = $createCustomerManagementInterface;
    }

    public function getSocialToken(CustomerInterface $customer) {
      $websiteId =$this->storeManager->getStore()->getWebsiteId();
      $customerData = $this->customer->setWebsiteId($websiteId)->loadByEmail($customer->getEmail());
      if($customerData->getId()){
              $customerToken = $this->tokenModelFactory->create();
              $tokenKey = $customerToken->createCustomerToken($customerData->getId())->getToken();
              return $tokenKey;
      } else {
          $registeredCustomerData = $this->createCustomerManagementInterface->postCreateCustomer($customer);
          $customerData = $this->customer->setWebsiteId($websiteId)->loadByEmail($registeredCustomerData->getEmail());
          if($customerData->getId()){
            $customerToken = $this->tokenModelFactory->create();
            $tokenKey = $customerToken->createCustomerToken($customerData->getId())->getToken();
            return $tokenKey;
          }
      }
      return "YOU MSG FOR CUSTOMER NOT FOUND";
    }

}
