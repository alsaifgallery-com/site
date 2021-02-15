<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\ThemeSettings\Block\Menu;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;
/**
 * Cms page content block
 */
class Items extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Catalog\Model\Category $categoryFactory,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        // used singleton (instead factory) because there exist dependencies on \Magento\Cms\Helper\Page
        $this->_page = $page;
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_pageFactory = $pageFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->objectManager = $objectManager;
        $this->pageConfig = $pageConfig;
    }

    public function getCategories() {
      $objectManager = $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $categoryFactory = $objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
      $categories = $categoryFactory->create()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('level', array('eq'=>2))
        ->addAttributeToFilter('is_active','1')
        ->addAttributeToFilter('include_in_menu','1')
        ->addAttributeToSort('position', 'ASC')
        ->setStore($this->_storeManager->getStore()); //categories from current store will be fetched
        return $categories;
      // foreach ($categories as $category){
      //   echo $category;
      // }
    }

    public function getCategory($categoryId) {
      $category = $this->_categoryFactory->load($categoryId);
      return $category->getChildrenCategories();
    }

    public function getCategoryImage($categoryId) {
      $category = $this->_categoryFactory->load($categoryId)->getThumbnail();
      return $category; //->getUrl();
    }

    public function getCategoryUrl($categoryId) {
      $category = $this->_categoryFactory->load($categoryId);
      return $category->getUrl();
    }

    public function getMediaUrl() {
        $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $media_dir;
    }
  }
