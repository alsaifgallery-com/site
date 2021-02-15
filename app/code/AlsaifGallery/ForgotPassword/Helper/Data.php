<?php

namespace AlsaifGallery\ForgotPassword\Helper;

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
     * @var \AlsaifGallery\RestAPIConfig\Model\OrderFactory
     */
    protected $slotOrdersFactory;

    protected $smsHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \AlsaifGallery\RestAPIConfig\Helper\Data $smsHelper
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->_date = $date;
        $this->smsHelper = $smsHelper;
    }

    public function getUnifonicConfigData($field)
    {
        return $this->smsHelper->getUnifonicConfigData($field);
    }
    public function sentVerifyCodeMessage($mobile, $code)
    {
        return $this->smsHelper->sentVerifyCodeMessage($mobile, $code);
    }
    public function sentVerifyCode($mobile)
    {
        return $this->sentVerifyCode($mobile);
    }
    public function sentVerifyNumber($mobile, $code)
    {
        return $this->smsHelper->sentVerifyNumber($mobile, $code);
    }

}
