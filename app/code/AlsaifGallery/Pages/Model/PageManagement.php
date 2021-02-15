<?php


namespace AlsaifGallery\Pages\Model;

class PageManagement implements \AlsaifGallery\Pages\Api\PageManagementInterface
{
    protected $pageRepository;
    protected $searchCriteriaBuilder;
    protected $storeManager;
    protected $pageFactory;
    public function __construct(
            \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
            \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Cms\Api\Data\PageInterfaceFactory $pageFactory
    ) {
        $this->pageRepository=$pageRepository;
        $this->searchCriteriaBuilder= $searchCriteriaBuilder;
        $this->storeManager=$storeManager;
        $this->pageFactory=$pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageByIdentifier($identifier) {
        $current_store = $this->storeManager->getStore()->getStoreId();
        $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('identifier', $identifier, 'eq')
                ->addFilter('is_active', 1, 'eq')
                ->addFilter('store_id', $current_store, 'eq')
                ->create();
        $pages = $this->pageRepository->getList($searchCriteria);       
        if(count($pages->getItems()) > 0){
        foreach ($pages->getItems() as $page) {
            $pageData['page_id'] = $page->getId();
            $pageData['title'] = $page->getTitle();
            $pageData['identifier'] = $page->getIdentifier();
            $pageData['content_heading'] = $page->getContentHeading();
            $pageData['content'] = $page->getContent();
            $pageData['is_active']=$page->isActive();
            $pageFactory = $this->pageFactory->create();
            $pageFactory->setData($pageData);  
            return $pageFactory;
        }
        }else{
          throw new \Magento\Framework\Exception\LocalizedException(
                        __("Sorry Page Identifier does not exist , check it and try again later")
                );   
        }
           
    }

}
