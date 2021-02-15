<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\AppConfigurations\Helper;

/**
 * Description of Data
 *
 * @author nada
 */
class Data {

    protected $scopeConfig;
    protected $storeManager;

    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Store\Model\StoreManagerInterface $storeManager) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getProductBaseImagePlaceholder() {
        $store = $this->storeManager->getStore();
        $defaultImage = $this->scopeConfig->getValue('catalog/placeholder/image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $defaultImage;
    }

    public function getProductSmallImagePlaceholder() {
        $store = $this->storeManager->getStore();
        $defaultImage = $this->scopeConfig->getValue('catalog/placeholder/small_image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $defaultImage;
    }

    public function getProductSwatchImagePlaceholder() {
        $store = $this->storeManager->getStore();
        $defaultImage = $this->scopeConfig->getValue('catalog/placeholder/swatch_image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $defaultImage;
    }

    public function getProductThumbnailImagePlaceholder() {
        $store = $this->storeManager->getStore();
        $defaultImage = $this->scopeConfig->getValue('catalog/placeholder/thumbnail_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $defaultImage;
    }

}
