<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\ForceUpdate\Model;

class ForceUpdateManagement implements \AlsaifGallery\ForceUpdate\Api\ForceUpdateManagementInterface
{


    /**
     * Configuration path to force update status
     */
    const XML_PATH_ENABLE_FORCE_UPDATE_IOS = 'updates/force_update/enable_force_update_ios';

    /**
     * Configuration path to Version number
     */
    const XML_PATH_CURRENT_VERSION_IOS = 'updates/force_update/version_number_ios';

    /**
     * Configuration path to force update status
     */
    const XML_PATH_ENABLE_FORCE_UPDATE_ANDROID = 'updates/force_update/enable_force_update_android';

    /**
     * Configuration path to Version number
     */
    const XML_PATH_CURRENT_VERSION_ANDROID = 'updates/force_update/version_number_android';

    /**
     * Configuration path to force update status
     */
    const XML_PATH_ENABLE_FORCE_UPDATE_HUAWEI = 'updates/force_update/enable_force_update_huawei';

    /**
     * Configuration path to Version number
     */
    const XML_PATH_CURRENT_VERSION_HUAWEI = 'updates/force_update/version_number_huawei';


    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
       $this->request = $request;
       $this->searchCriteriaBuilder = $searchCriteriaBuilder;
       $this->orderRepository = $orderRepository;
       $this->invoiceService = $invoiceService;
       $this->transectionFactory = $transactionFactory;
       $this->invoiceSender = $invoiceSender;
       $this->customerRepositoryInterface = $customerRepositoryInterface;
       $this->orderCollectionFactory = $orderCollectionFactory;
       $this->_customerFactory = $customerFactory;
       $this->_customer = $customers;
       $this->logger = $logger;
       $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getForceUpdate()
    {
        $versionNumber = $this->request->getParam('versionNumber');
        $deviceType = $this->request->getParam('deviceType');
        $version_number_ios = $this->scopeConfig->getValue(self::XML_PATH_CURRENT_VERSION_IOS,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $status_ios =  $this->scopeConfig->getValue(self::XML_PATH_ENABLE_FORCE_UPDATE_IOS,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $version_number_android = $this->scopeConfig->getValue(self::XML_PATH_CURRENT_VERSION_ANDROID,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $status_android =  $this->scopeConfig->getValue(self::XML_PATH_ENABLE_FORCE_UPDATE_ANDROID,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $version_number_huawei = $this->scopeConfig->getValue(self::XML_PATH_CURRENT_VERSION_HUAWEI,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $status_huawei =  $this->scopeConfig->getValue(self::XML_PATH_ENABLE_FORCE_UPDATE_HUAWEI,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if(strcasecmp("ios", $deviceType) == 0) {
          if($status_ios) {
            if($version_number_ios == $versionNumber) {
              return true;
            }else {
              return false;
            }
          } else {
            return false;
          }
        } elseif(strcasecmp("android", $deviceType) == 0) {
          if($status_android) {
            if($version_number_android == $versionNumber) {
              return true;
            }else {
              return false;
            }
          } else {
            return false;
          }
        } elseif(strcasecmp("huawei", $deviceType) == 0) {
          if($status_huawei) {
            if($version_number_huawei == $versionNumber) {
              return true;
            }else {
              return false;
            }
          } else {
            return false;
          }
        } else {
          return "Device type is not recognized";
        }
    }
}
