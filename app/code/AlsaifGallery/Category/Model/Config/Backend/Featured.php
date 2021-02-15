<?php

namespace AlsaifGallery\Category\Model\Config\Backend;


class Featured extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    protected $categotyCollectionFactory;
//    protected $limit = 8;
//    protected $limit = 88; // not to add to git
    
    protected $storeManager;
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categotyCollectionFactory,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->categotyCollectionFactory = $categotyCollectionFactory;
        $this->storeManager=$storeManager;
    }

    /**
     * Validate process
     *
     * @param \Magento\Framework\DataObject $object
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate($object)
    {
        $currentStore = $this->storeManager->getStore();
        $limit=$this->_scopeConfig->getValue('appconfigurations_setting/configs/homecategorieslimit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);
        $retun = parent::validate($object);
        
        $attributeCode = $this->getAttribute()->getName();
        $object->getData($attributeCode);
        $edited = True;

        if ($object->getData($attributeCode)) {
                    $currentId= $object->getData('entity_id');
            $categories = $this->categotyCollectionFactory->create()
                   ->addAttributeToSelect('*')
                   ->addAttributeToFilter('is_featured',1)
                   ->addAttributeToFilter('is_active',1);
           foreach ($categories as $category){
               if ($category->getId() == $currentId){
                   $edited = false;
               }
           }
             
            $catsCount = count( $categories );
            if ($catsCount >= $limit && $edited) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can add only %1 as Featured category. Limit reached',$limit)
                );
            }
        }

        return true;
    }
}
