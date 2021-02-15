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
 * @package     Mageplaza_SeoDashboard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoDashboard\Controller\Adminhtml\Duplicate;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\SeoDashboard\Controller\Adminhtml\Report;

/**
 * Class Grid
 * @package Mageplaza\SeoDashboard\Controller\Adminhtml\Duplicate
 */
class Grid extends Report
{
    /**
     * @type \Magento\Framework\View\Result\LayoutFactory|null
     */
    protected $_resultLayoutFactory = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Action\Context $context
    )
    {
        $this->_resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }
}