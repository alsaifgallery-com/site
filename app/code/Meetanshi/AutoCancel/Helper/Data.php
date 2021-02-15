<?php


namespace Meetanshi\AutoCancel\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const ENABLE = 'admin_autocancel/general/enable';
    const SENDER = 'admin_autocancel/general/email_sender';
    const PAYMENT = 'admin_autocancel/general/payments';
    const STATUS = 'admin_autocancel/general/status';
    const EMAIL_TEMPLATE = 'admin_autocancel/general/email_template';
    const ADMIN_EMAIL = 'admin_autocancel/general/admin_email';
    const CUSTOMER_EMAIL = 'admin_autocancel/general/customer_email';
    const CUSTOMER_EMAIL_TEMPLATE = 'admin_autocancel/general/customer_email_template';
    const DAYS = 'admin_autocancel/general/days';

    private $timezone;
    private $storeManager;
    private $inlineTranslation;
    private $transportBuilder;

    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    public function getAfterDate($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $date = date('Y-m-d', strtotime(str_replace('-','/', $this->scopeConfig->getValue(self::DAYS, $scope))));
        return $date;
    }

    public function getAutoCancel($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        try {
            if (!$this->scopeConfig->getValue(self::ENABLE, $scope)) :
                return false;
            else :
                $configData = date_create(date('d-m-Y', strtotime(str_replace('-','/', $this->scopeConfig->getValue(self::DAYS, $scope)))));
                $currentData = date_create(date("d-m-Y", strtotime(str_replace('-','/', $this->getCurrentTime()))));
                $dayDiffernt = date_diff($currentData, $configData);
                $dayDiffernt = $dayDiffernt->format("%a");
                if ($dayDiffernt > 0) :
                    return true;
                else :
                    return false;
                endif;
            endif;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCurrentTime()
    {
        try {
            return date("Y-m-d H:i:s");
            //return $this->timezone->date()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getStatusConfig($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::STATUS, $scope);
    }

    public function getPaymentConfig($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::PAYMENT, $scope);
    }

    public function sendCustomMailSendMethod($config)
    {
        $config = ['incrementid' => $config];
        try {
            $config['storename'] = $this->getStoreName();
            $this->inlineTranslation->suspend();
            $this->generateTemplate($config);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function sendCustomerMail($config,$email,$name)
    {
        $config = ['incrementid' => $config, 'customerName' => $name];

        try {
            $config['storename'] = $this->getStoreName();
            $this->inlineTranslation->suspend();
            $this->generateCustomerTemplate($config,$email,$name);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return 1;
    }

    public function generateCustomerTemplate($config,$email,$name)
    {
        try {
            $this->transportBuilder->setTemplateIdentifier($this->getCustomerEmailTemplate())
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($config)
                ->setFrom($this->scopeConfig->getValue(self::SENDER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT))
                ->addTo($email, $name);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $this;
    }

    public function getStoreName($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function generateTemplate($config)
    {
        $recipient = $this->getRecipient();
        try {
            $this->transportBuilder->setTemplateIdentifier($this->getEmailTemplate())
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($config)
                ->setFrom($this->scopeConfig->getValue(self::SENDER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT))
                ->addTo($recipient['email'], $recipient['name']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $this;
    }

    public function getRecipient($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $value = $this->scopeConfig->getValue(
            self::SENDER,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $recipient = [
            'name' => $this->scopeConfig->getValue('trans_email/ident_' . $value . '/name', ScopeConfigInterface::SCOPE_TYPE_DEFAULT),
            'email' => $this->scopeConfig->getValue('trans_email/ident_' . $value . '/email', ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
        ];
        return $recipient;
    }

    public function isSendAdminEmail() {
        return $this->scopeConfig->getValue(self::ADMIN_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
    public function isSendCustomerEmail() {
        return $this->scopeConfig->getValue(self::CUSTOMER_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
    public function getEmailTemplate($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::EMAIL_TEMPLATE, $scope);
    }
    public function getCustomerEmailTemplate($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_EMAIL_TEMPLATE, $scope);
    }
}
