<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Controller\Adminhtml\Report;
use Magento\Backend\App\Action;

/**
 * Class Usage
 * @package Amasty\Xcoupon\Controller\Adminhtml\Report
 * @author Artem Brunevski
 */
class Usage extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Amasty_Xcoupon::report_usage';

    /** @var \Magento\Backend\Model\View\Result\ForwardFactory  */
    protected $resultForwardFactory;

    /** @var \Magento\Framework\View\Result\PageFactory  */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('Amasty_Xcoupon::report_usage');
        $resultPage->getConfig()->getTitle()->prepend(__('Usage Report'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Usage Report'), __('Usage Report'));

        return $resultPage;
    }
}