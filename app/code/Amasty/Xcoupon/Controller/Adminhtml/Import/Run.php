<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class Run
 * @package masty\Xcoupon\Controller\Adminhtml\Import
 * @author Artem Brunevski
 */
class Run extends \Magento\Backend\App\Action
{
    /**
     * @var \Amasty\Xcoupon\Model\Import
     */
    protected $import;

    /**
     * @var \Amasty\Xcoupon\Model\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Action\Context $context
     * @param \Amasty\Xcoupon\Model\Import $import
     * @param \Amasty\Xcoupon\Model\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Amasty\Xcoupon\Model\Import $import,
        \Amasty\Xcoupon\Model\UploaderFactory $uploaderFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->import = $import;
        $this->uploaderFactory = $uploaderFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesRule::quote');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = [];
        $uploader = false;

        try {
            $ruleId = $this->getRequest()->getParam('rule_id');
            $clean = $this->getRequest()->getParam('clean', 0);

            if ($ruleId) {
                /** @var \Amasty\Xcoupon\Model\Uploader $uploader */
                $uploader = $this->uploaderFactory->create();

                if (!$this->getRequest()->getFiles('file')) {
                    throw new LocalizedException(__('No import file chosen'));
                }

                if ($file = $uploader->upload('file')) {
                    if ($clean) {
                        $this->import->clean($ruleId);
                    }

                    $this->import->run($file, $ruleId);

                    $result['messages'] = __('Import has been done.');
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $result['error'] = $e->getMessage();
        }

        if ($uploader && !isset($result['error'])) {
            try {
                $uploader->delete();
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }

        return $this->resultJsonFactory->create()->setData($result);
    }
}
