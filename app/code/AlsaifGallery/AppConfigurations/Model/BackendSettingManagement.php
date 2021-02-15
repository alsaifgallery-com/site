<?php


namespace AlsaifGallery\AppConfigurations\Model;


class BackendSettingManagement implements \AlsaifGallery\AppConfigurations\Api\BackendSettingManagementInterface
{

    public $scopeConfig;
    
    public $storeManager;
    
    public $configStructure;
    
    public  $abstractStructure;
    protected $encryptor;


    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Config\Model\Config\Structure $configStructure,
             \Magento\Framework\Encryption\EncryptorInterface $encryptor

            ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->configStructure = $configStructure;
        $this->encryptor = $encryptor;
//        $this->abstractStructure = $abstractStructure;
    }

    /**
     * {@inheritdoc}
     * 
     */
    public function getBackendSetting()
    {
        $encryptedArr = ["settings/checkoutcom_configuration/public_key"];
        $storeId = $this->storeManager->getStore()->getId();
        $configuirations = array();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $data = $this->scopeConfig->getValue('appconfigurations_setting/general/appconfigurations');
        $dataConfiguirations = explode(',', $data);
        if (array_search('web/unsecure/base_url', $dataConfiguirations) == false) {
            $configuirations['web/unsecure/base_url'] = $this->storeManager->getStore()->getBaseUrl();
        }
        if (array_search('web/unsecure/base_media_url', $dataConfiguirations) == false) {
            $configuirations['web/unsecure/base_media_url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
        if (array_search('web/secure/base_url', $dataConfiguirations) == false) {
            $configuirations['web/secure/base_url'] = $this->storeManager->getStore()->getBaseUrl();
        }
        if (array_search('web/secure/base_media_url', $dataConfiguirations) == false) {
            $configuirations['web/secure/base_media_url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
        foreach ($dataConfiguirations as $config) {
            if ($config == 'appconfigurations_setting/configs/app_image') {
                $configuirations[$config] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '/blog/post4/' . $this->scopeConfig->getValue($config, $storeScope, $storeId);
            } else {
                // Return a decrypted value for encrypted fields
                if ( in_array($config, $encryptedArr)) {
                    $value = $this->scopeConfig->getValue($config, $storeScope, $storeId);
                    $configuirations[$config] = $this->encryptor->decrypt($value);
                }else{
                    $configuirations[$config] = $this->scopeConfig->getValue($config, $storeScope, $storeId);
                }
            }
        }

        return array($configuirations);
    }
}
