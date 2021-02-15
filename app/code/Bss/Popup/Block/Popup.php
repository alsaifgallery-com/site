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

use Bss\Popup\Model\Source\PageDisplay;

class Popup extends \Magento\Framework\View\Element\Template
{
    /**
     * Block Type Page
     *
     * @var int
     */
    protected $blockType;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Popup Helper
     *
     * @var \Bss\Popup\Helper\Data
     */
    protected $helper;

    /**
     * Filter Provider
     *
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * Json Encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $collectionPopup;

    /**
     * Popup constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Bss\Popup\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Bss\Popup\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Bss\Popup\Model\ResourceModel\Popup\Collection $collectionPopup,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->helper = $helper;
        $this->coreRegistry = $registry;
        $this->storeManager = $context->getStoreManager();
        $this->jsonEncoder = $jsonEncoder;
        $this->collectionPopup = $collectionPopup;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve block type
     *
     * @return int
     */
    public function getBlockType()
    {
        if (false !== strpos($this->getNameInLayout(), 'popup_product')) {
            $this->blockType = PageDisplay::PRODUCT_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'popup_category')) {
            $this->blockType = PageDisplay::CATEGORY_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'popup_home')) {
            $this->blockType = PageDisplay::HOME_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'popup_cart')) {
            $this->blockType = PageDisplay::CART_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'popup_checkout')) {
            $this->blockType = PageDisplay::CHECKOUT_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'popup_default')) {
            $this->blockType = PageDisplay::ALL_OTHER_PAGE;
        }
        return $this->blockType;
    }

    /**
     * Get Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $storeId;
    }

    /**
     * Get Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('product');
        $productId = (!empty($product))? $product->getId() : 0;
        return $productId;
    }

    /**
     * Get Category Id
     *
     * @return int
     */
    public function getCategoryId()
    {
        $category = $this->coreRegistry->registry('current_category');
        $categoryId = (!empty($category))? $category->getId() : 0;
        return $categoryId;
    }

    /**
     * Get Page Information
     *
     * @return array
     */
    public function getPageInformation()
    {
        $pageInformation = [];
        $request = $this->getRequest();
        $pageInformation['routeName'] = $request->getRouteName();
        $pageInformation['moduleName'] = $request->getModuleName();
        $pageInformation['controllerName'] = $request->getControllerName();
        $pageInformation['actionName'] = $request->getActionName();
        $pageInformation['uri'] = $request->getRequestUri();
        return $this->jsonEncoder->encode($pageInformation);
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
     * @param $stringContent
     * @return string
     * @throws \Exception
     */
    public function filterContent($stringContent)
    {
        return $this->filterProvider->getPageFilter()->filter($stringContent);
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
     * Get All Popup
     *
     * @return array
     */
    public function getAllPopups()
    {
        $popups = $this->helper->getPopup($this->getBlockType());

        foreach ($popups as $key => $value) {
            if (!$value['status']
                || !$this->inStoreView($value['storeview'], $this->getStoreId())
                || !$this->inCustomerGroup($value['customer_group'], $this->helper->getCustomerGroupId())
                || $this->excludeProduct($value['exclude_product'], $this->getProductId())
                || $this->excludeCategory($value['exclude_category'], $this->getCategoryId())
            ) {
                unset($popups[$key]);
            }
        }

        return $popups;
    }

    public function getPagesViewed()
    {
        return $this->helper->getSessionPageViewedByCustomer();
    }

    /**
     * Get Popup Animation
     *
     * @param array $popup
     * @return string
     */
    public function getAnimation($popup)
    {
        if ($popup['effect_display'] == \Bss\Popup\Model\Source\Animation::ZOOM) {
            return "mfp-zoom-in";
        }
        if ($popup['effect_display'] == \Bss\Popup\Model\Source\Animation::HORIZONTAL) {
            return "mfp-move-horizontal";
        }
        if ($popup['effect_display'] == \Bss\Popup\Model\Source\Animation::FROM_TOP) {
            return "mfp-move-from-top";
        }
        if ($popup['effect_display'] == \Bss\Popup\Model\Source\Animation::UNFOLD_3D) {
            return "mfp-3d-unfold";
        }
        if ($popup['effect_display'] == \Bss\Popup\Model\Source\Animation::ZOOM_OUT) {
            return "mfp-zoom-out";
        }
        return " ";
    }

    public function popupIsAllowedDisplay($popup)
    {
        return $this->helper->popupIsAllowedDisplay($popup);
    }

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

}
