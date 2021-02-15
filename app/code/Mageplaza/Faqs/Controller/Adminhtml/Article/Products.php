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

namespace Mageplaza\Faqs\Controller\Adminhtml\Article;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\Faqs\Controller\Adminhtml\Article;
use Mageplaza\Faqs\Model\ArticleFactory;

/**
 * Class Products
 * @package Mageplaza\Faqs\Controller\Adminhtml\Article
 */
class Products extends Article
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Products constructor.
     * @param LayoutFactory $resultLayoutFactory
     * @param ArticleFactory $articleFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        ArticleFactory $articleFactory,
        Registry $coreRegistry,
        Context $context
    )
    {
        parent::__construct($articleFactory, $coreRegistry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->initArticle(true);

        return $this->resultLayoutFactory->create();
    }
}
