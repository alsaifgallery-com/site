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

namespace Mageplaza\Faqs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Category
 * @package Mageplaza\Faqs\Controller\Adminhtml
 */
abstract class Category extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_Faqs::category';

    /**
     * Category model factory
     *
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Article constructor.
     * @param CategoryFactory $categoryFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        Registry $coreRegistry,
        Context $context
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->coreRegistry    = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @return bool|\Mageplaza\Faqs\Model\Category
     */
    protected function initCategory($register = false)
    {
        $categoryId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\Faqs\Model\Category $category */
        $category = $this->categoryFactory->create();

        if ($categoryId) {
            $category->load($categoryId);
            if (!$category->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_faqs_category', $category);
        }

        return $category;
    }
}
