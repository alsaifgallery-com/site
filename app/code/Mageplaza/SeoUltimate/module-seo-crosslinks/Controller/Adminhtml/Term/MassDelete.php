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
 * @package     Mageplaza_SeoCrosslinks
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoCrosslinks\Controller\Adminhtml\Term;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\SeoCrosslinks\Model\ResourceModel\Term\CollectionFactory;

/**
 * Class MassDelete
 * @package Mageplaza\SeoCrosslinks\Controller\Adminhtml\Term
 */
class MassDelete extends Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Collection Factory
     *
     * @var \Mageplaza\SeoCrosslinks\Model\ResourceModel\Term\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $delete = 0;
        foreach ($collection as $item) {
            /** @var \Mageplaza\SeoCrosslinks\Model\Term $item */
            $item->delete();
            $delete++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
