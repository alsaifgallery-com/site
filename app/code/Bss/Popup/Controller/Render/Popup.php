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
namespace Bss\Popup\Controller\Render;
 
class Popup extends \Magento\Framework\App\Action\Action
{
    /**
     * Result Raw Factory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * Layout Factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Helper
     *
     * @var \Bss\Popup\Helper\Data
     */
    protected $helper;

    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Bss\Popup\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Bss\Popup\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper

    ) {
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory; 
        $this->layoutFactory = $layoutFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $pageDisplay = $this->getRequest()->getPost('pageDisplay');
            $customerGroup = $this->helper->getCustomerGroupId();
            $storeViewId = $this->getRequest()->getPost('storeViewID');
            $productId = $this->getRequest()->getPost('productId');
            $categoryId = $this->getRequest()->getPost('categoryId');
            $pagesViewed = $this->helper->getSessionPageViewedByCustomer();

            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $this->layoutFactory->create();

            $block = $layout->createBlock(\Bss\Popup\Block\Ajax::class);
            $block->setBlockPage($pageDisplay);
            $block->setStoreViewId($storeViewId);
            $block->setCustomerGroupId($customerGroup);
            $block->setProductId($productId);
            $block->setCategoryId($categoryId);
            $block->setPagesViewed($pagesViewed);
            $block->setTemplate('Bss_Popup::ajax.phtml');
            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            return $this->getResponse()->setBody($block->toHtml());

        } else {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('no-route');
        }
    }

}
