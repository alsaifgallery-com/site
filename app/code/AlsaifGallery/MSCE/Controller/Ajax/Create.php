<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\MSCE\Controller\Ajax;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Create extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Json\Helper\Data $helper,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CookieManagerInterface $cookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager     = $storeManager;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerFactory  = $customerFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->cookieManager = $cookieManager ?:
            ObjectManager::getInstance()->get(CookieManagerInterface::class);
        $this->cookieMetadataFactory = $cookieMetadataFactory ?:
            ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        parent::__construct($context);
    }


    /**
     * Get account redirect.
     *
     * @deprecated 100.0.10
     * @return AccountRedirect
     */
    protected function getAccountRedirect()
    {
        if (!is_object($this->accountRedirect)) {
            $this->accountRedirect = ObjectManager::getInstance()->get(AccountRedirect::class);
        }
        return $this->accountRedirect;
    }

    /**
     * Account redirect setter for unit tests.
     *
     * @deprecated 100.0.10
     * @param AccountRedirect $value
     * @return void
     */
    public function setAccountRedirect($value)
    {
        $this->accountRedirect = $value;
    }

    /**
     * Initializes config dependency.
     *
     * @deprecated 100.0.10
     * @return ScopeConfigInterface
     */
    protected function getScopeConfig()
    {
        if (!is_object($this->scopeConfig)) {
            $this->scopeConfig = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        }
        return $this->scopeConfig;
    }

    /**
     * Sets config dependency.
     *
     * @deprecated 100.0.10
     * @param ScopeConfigInterface $value
     * @return void
     */
    public function setScopeConfig($value)
    {
        $this->scopeConfig = $value;
    }


    /**
     * Execute view action
     *
     * @return string
     */
    public function execute()
    {
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors' => false,
            'message' => __('Login successful.')
        ];
        try {
            $firstname= '';
            $lastname='';
            $fullname = $credentials['fullname'];
            $name = explode(" ",$fullname);
            if(count($name) <= 1) {
              $firstname = $name[0];
              $lastname = $name[0];
            } else {
            	$firstname = $name[0];
              $lastname = $name[1];
            }
            $username = $credentials['username'];
            $password = $credentials['password'];
            // Get Website ID
            $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
            //
            // Instantiate object (this is the most important part)
            $customer   = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            //
            // Preparing data for new customer
            $customer->setEmail($username);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setPassword($password);
            //
            // Save data
            $customer->save();
            $customer->sendNewAccountEmail();

            $response = [
                'errors' => false,
                'message' => 'Customer account created successfully.',
            ];


        } catch (LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __('This email is already used by another customer'),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
