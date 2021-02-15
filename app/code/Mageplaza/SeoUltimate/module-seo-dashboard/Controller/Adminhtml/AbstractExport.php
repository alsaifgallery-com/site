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

namespace Mageplaza\SeoDashboard\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

/***
 * Class AbstractExport
 * @package Mageplaza\SeoDashboard\Controller\Adminhtml\Export
 */
abstract class AbstractExport extends Action
{
    /***
     * @var string
     */
    protected $extension = 'csv';

    /***
     * @var string
     */
    protected $fileName = 'export';

    /***
     * Default 404 pages
     * @var string
     */
    protected $block = 'Mageplaza\SeoDashboard\Block\Adminhtml\NoRoute\Grid';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory
    )
    {
        $this->_fileFactory = $fileFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $content = $this->_view->getLayout()->createBlock($this->block);
        if ($this->extension == 'xml') {
            $content = $content->getExcelFile();
        } else {
            $content = $content->getCsvFile();
        }

        return $this->_fileFactory->create($this->fileName . '.' . $this->extension, $content, DirectoryList::VAR_DIR);
    }
}
