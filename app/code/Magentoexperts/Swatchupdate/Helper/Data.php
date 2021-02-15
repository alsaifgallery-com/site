<?php
namespace Magentoexperts\Swatchupdate\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_MODULE_ENABLE = 'swatchupdate/general/enable';

    const XML_PATH_MODULE_SKU = 'swatchupdate/general/sku';

    const XML_PATH_MODULE_NAME = 'swatchupdate/general/name';

    const XML_PATH_MODULE_DESCRIPTION = 'swatchupdate/general/description';

    const XML_PATH_MODULE_SHORTDESCRIPTION = 'swatchupdate/general/shortdescription';

    const XML_PATH_MODULE_ADDTIONALINFO = 'swatchupdate/general/addtionalinfo';


    public function getModuleEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDynamicSku()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_SKU, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    public function getDynamicName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDynamicDescription()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_DESCRIPTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDynamicShortDescription()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_SHORTDESCRIPTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDynamicAddtionalinfo()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_ADDTIONALINFO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}