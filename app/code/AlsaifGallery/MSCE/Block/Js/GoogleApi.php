<?php


namespace AlsaifGallery\MSCE\Block\Js;

use Magento\Framework\View\Element\Template;

/**
 * Class GoogleApi
 */
class GoogleApi extends Template
{
    /**
     * GoogleApi constructor.
     *
     * @param Template\Context $context
     * @param array $fieldsMap
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Template\Context $context,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);

    }

    /**
     * @return array
     */
    public function getFieldsMap()
    {
        return $this->fieldsMap;
    }

    // /**
    //  * @return string
    //  */
    // public function getApiKey()
    // {
    //     return $this->configResolver->getApiKey();
    // }
    //
    // /**
    //  * @return string
    //  */
    // public function isEnabled()
    // {
    //     return $this->configResolver->getIsEnabled();
    // }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        $storeCode = $this->_storeManager->getStore()->getCode();
        $locale = substr($storeCode, -2);
        return $locale;
    }
}
