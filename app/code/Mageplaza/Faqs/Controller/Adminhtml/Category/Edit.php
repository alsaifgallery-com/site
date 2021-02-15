<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Faqs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Faqs\Controller\Adminhtml\Category;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Edit
 * @package Mageplaza\Faqs\Controller\Adminhtml\Category
 */
class Edit extends Category
{
    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($categoryFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Mageplaza\Faqs\Model\Category $category */
        $category = $this->initCategory();

        if (!$category) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_faqs_category_data', true);
        if (!empty($data)) {
            $category->setData($data);
        }

        $this->coreRegistry->register('mageplaza_faqs_category', $category);

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Faqs::category');
        $resultPage->getConfig()->getTitle()->set(__('Manage Categories'));

        $title = $category->getId() ? __('Edit "%1"', $category->getName()) : __('New Category');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
