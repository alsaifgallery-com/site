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
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\Faqs\Controller\Adminhtml\Category;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Articles
 * @package Mageplaza\Faqs\Controller\Adminhtml\Category
 */
class Articles extends Category
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Articles constructor.
     * @param LayoutFactory $resultLayoutFactory
     * @param CategoryFactory $categoryFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        CategoryFactory $categoryFactory,
        Registry $coreRegistry,
        Context $context
    )
    {
        parent::__construct($categoryFactory, $coreRegistry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->initCategory(true);

        return $this->resultLayoutFactory->create();
    }
}
