<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Popup
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Popup\Block;

use Bss\Popup\Model\Source\Animation;

class Ajax extends \Magento\Framework\View\Element\Template
{

    /**
     * Block Page
     *
     * @var int
     */
    protected $blockPage;

    /**
     * Helper
     *
     * @var \Bss\Popup\Helper\Data
     */
    protected $helper;

    /**
     * Store View Id
     *
     * @var int
     */
    protected $storeViewId;

    /**
     * Customer Group Id
     *
     * @var int
     */
    protected $customerGroupId;

    /**
     * Product Id
     *
     * @var int
     */
    protected $productId;

    /**
     * Category Id
     *
     * @var int
     */
    protected $categoryId;

    /**
     * Filter Provider
     *
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * Pages Viewed
     *
     * @var array
     */
    protected $pagesViewed;

    /**
     * Module Manager
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Ajax constructor.
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Bss\Popup\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Bss\Popup\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->helper = $helper;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }

    /**
     * Initialize page request
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $request = $this->getRequest();
        $pageInformation = $request->getPostValue('pageInformation');
        if (!empty($pageInformation)) {
            $request->setModuleName($pageInformation['moduleName']);
            $request->setControllerName($pageInformation['controllerName']);
            $request->setActionName($pageInformation['actionName']);
            $request->setRequestUri($pageInformation['uri']);
        }
    }

    /**
     * Get Block Page
     *
     * @return int
     */
    public function getBlockPage()
    {
        return $this->blockPage;
    }

    /**
     * Set Block Page
     *
     * @param int $blockPage
     * @return void
     */
    public function setBlockPage($blockPage)
    {
        $this->blockPage = $blockPage;
    }

    /**
     * Get Popup
     *
     * @return array
     */
    public function getPopup()
    {
        $popups = $this->getAllPopups();
        $popup = [];
        $minPriority = "";
        $i = 1;
        foreach ($popups as $value) {
            if ($i == 1) {
                $minPriority = $value['priority'];
            }
            if ($value['priority'] <= $minPriority) {
                $minPriority = $value['priority'];
                $popup = $value;
            }
            $i++;
        }
        return $popup;
    }

    /**
     * Get Popup Animation
     *
     * @param array $popup
     * @return string
     */
    public function getAnimation($popup)
    {
        if ($popup['effect_display'] == Animation::ZOOM) {
            return "mfp-zoom-in";
        }
        if ($popup['effect_display'] == Animation::HORIZONTAL) {
            return "mfp-move-horizontal";
        }
        if ($popup['effect_display'] == Animation::FROM_TOP) {
            return "mfp-move-from-top";
        }
        if ($popup['effect_display'] == Animation::UNFOLD_3D) {
            return "mfp-3d-unfold";
        }
        if ($popup['effect_display'] == Animation::ZOOM_OUT) {
            return "mfp-zoom-out";
        }
        return " ";
    }

    /**
     * Get Popups
     *
     * @return array
     */
    public function getAllPopups()
    {
        $popups = $this->helper->getPopup($this->blockPage);
        foreach ($popups as $key => $value) {
            if (!$value['status']
                || !$this->inStoreView($value['storeview'], $this->storeViewId)
                || !$this->inCustomerGroup($value['customer_group'], $this->customerGroupId)
                || $this->excludeProduct($value['exclude_product'], $this->productId)
                || $this->excludeCategory($value['exclude_category'], $this->categoryId)
            ) {
                unset($popups[$key]);
            }
        }
        return $popups;
    }

    /**
     * Set Store View Id
     *
     * @param int $storeViewId
     * @return void
     */
    public function setStoreViewId($storeViewId)
    {
        $this->storeViewId = $storeViewId;
    }

    /**
     * Set Customer Group Id
     *
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * Check In Store View
     *
     * @param String $popupStoreView
     * @param String $storeId
     * @return bool
     */
    public function inStoreView($popupStoreView, $storeId)
    {
        $listStoreView = explode(",", $popupStoreView);
        if (in_array($storeId, $listStoreView)
            || in_array(0, $listStoreView)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check In Customer Group
     *
     * @param String $popupCustomerGroup
     * @param String $customerGroupId
     * @return bool
     */
    public function inCustomerGroup($popupCustomerGroup, $customerGroupId)
    {
        $listCustomerGroup = explode(",", $popupCustomerGroup);
        if (in_array($customerGroupId, $listCustomerGroup)) {
            return true;
        }
        return false;
    }

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return void
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Set Category Id
     *
     * @param int $categoryId
     * @return void
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Check Exclude Products
     *
     * @param String $listProduct
     * @param int $productId
     * @return bool
     */
    public function excludeProduct($listProduct, $productId)
    {
        $listProduct = explode(",", $listProduct);
        if ($productId !=0 && in_array($productId, $listProduct)) {
            return true;
        }
        return false;
    }

    /**
     * Check Exclude Category
     *
     * @param string $listCategory
     * @param int $categoryId
     * @return bool
     */
    public function excludeCategory($listCategory, $categoryId)
    {
        $listCategory = explode(",", $listCategory);
        if ($categoryId !=0 && in_array($categoryId, $listCategory)) {
            return true;
        }
        return false;
    }

    /**
     * Get Helper
     *
     * @return \Bss\Popup\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Filter Content
     *
     * @param string $stringContent
     * @return string
     */
    public function filterContent($stringContent)
    {
        return $this->filterProvider->getPageFilter()->filter($stringContent);
    }

    /**
     * Get List Pages Viewed
     *
     * @return array
     */
    public function getPagesViewed()
    {
        return $this->pagesViewed;
    }

    /**
     * Set List Pages Viewed
     *
     * @param array $pagesViewed
     * @return void
     */
    public function setPagesViewed($pagesViewed)
    {
        $this->pagesViewed = $pagesViewed;
    }

    /**
     * Check Bss Ajax Cart Install
     * @return bool
     */
    public function checkBssAjaxCartInstall()
    {
        return $this->moduleManager->isOutputEnabled('Bss_AjaxCart');
    }

}
