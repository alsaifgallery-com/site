<?php

namespace AlsaifGallery\RestAPIConfig\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->_date = $date;
    }

    /**
     * Retrieve information from time slot configuration.
     *
     * @param string $field
     *
     * @return void|false|string
     */
    public function getConfigData($field)
    {
        $path = 'time_slots_delivery/general/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }
    public function getUnifonicConfigData($field)
    {
        $path = 'unifonic_settings/general/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function sentVerifyCodeMessage($mobile, $code)
    {
        $textBody = $this->getUnifonicConfigData('unifonic_verify_message');
        if (empty($textBody)) {
            $textBody = __('Your Verification Code is : ');
        }
        $appId = $this->getUnifonicConfigData('api_key');
        $appBaseURL = $this->getUnifonicConfigData('api_url'); //Get this from Unifonic module
        //die( $appBaseURL );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $appBaseURL . "rest/Messages/Send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, 'AppSid=' . $appId . '&Recipient=' . $mobile . '&Body=' . $textBody . $code);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function sentVerifyCode($mobile)
    {
        $textBody = $this->getUnifonicConfigData('unifonic_verify_message');
        if (empty($textBody)) {
            $textBody = __('Your Verification Code is : ');
        }
        $appId = $this->getUnifonicConfigData('api_key');
        $appBaseURL = $this->getUnifonicConfigData('api_url');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $appBaseURL . "rest/Verify/GetCode");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, 'AppSid=' . $appId . '&Recipient=' . $mobile . '&Body=' . $textBody);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
    public function sentVerifyNumber($mobile, $code)
    {
        $appId = $this->getUnifonicConfigData('api_key');
        $appBaseURL = $this->getUnifonicConfigData('api_url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $appBaseURL . "rest/Verify/VerifyNumber");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'AppSid=' . $appId . '&Recipient=' . $mobile . '&PassCode=' . $code);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
