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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Faqs\Model\ResourceModel\Category\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class MassDelete
 * @package Mageplaza\Faqs\Controller\Adminhtml\Category
 */
class MassDelete extends Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     *
     * @var LoggerInterface
     */
    public $logger;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    )
    {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger            = $logger;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $ids        = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        $collection = ($ids)
            ? $this->collectionFactory->create()->addFieldToFilter('main_table.category_id', ["in" => $ids])
            : $this->filter->getCollection($this->collectionFactory->create());

        try {
            $collection->walk('delete');

            $this->messageManager->addSuccessMessage(__('Categories has been deleted.'));
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Something wrong when deleting the selected Categories.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
