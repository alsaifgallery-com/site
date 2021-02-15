<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\Orders\Controller\Adminhtml\Order\Status;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Sales\Controller\Adminhtml\Order\Status as StatusAction;
use Magento\Sales\Model\Order\Status;

/**
 * Description of Save
 *
 * @author nada
 */
class Save extends StatusAction implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->uploader = $uploader;
        parent::__construct($context, $coreRegistry);
//       parent::__construct($context, $resultRawFactory, $resultJsonFactory, $layoutFactory, $dateFilter, $storeManager, $eavConfig);
    }
    public function execute()
    {
//      var_dump($_FILES);
        //      die();
        //            die('nada');
        //            var_dump('nada');

//        var_dump($_FILES['image']);
        //        die('testnada');
        $data = $this->getRequest()->getPostValue();
        if (isset($_FILES['icon']) && isset($_FILES['icon']['name']) && strlen($_FILES['icon']['name'])) {
//            die('true');
            try {
                $base_media_path = 'orders/isballeh/icons';
                $uploader = $this->uploader->create(
                    ['fileId' => 'icon']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath($base_media_path)
                );
                $data['icon'] = $base_media_path . $result['file'];

            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        } else {
            if (isset($data['icon']) && isset($data['icon']['value'])) {
                if (isset($data['icon']['delete'])) {
                    $data['icon'] = null;
                    $data['delete_image'] = true;
                } elseif (isset($data['icon']['value'])) {
                    $data['icon'] = $data['icon']['value'];
                } else {
                    $data['icon'] = null;
                }
            }
        }

        $isNew = $this->getRequest()->getParam('is_new');

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $statusCode = $this->getRequest()->getParam('status');

            //filter tags in labels/status
            /** @var $filterManager FilterManager */
            $filterManager = $this->_objectManager->get(FilterManager::class);
            if ($isNew) {
                $statusCode = $data['status'] = $filterManager->stripTags($data['status']);
            }
            $data['label'] = $filterManager->stripTags($data['label']);
            if (!isset($data['store_labels'])) {
                $data['store_labels'] = [];
            }

            foreach ($data['store_labels'] as &$label) {
                $label = $filterManager->stripTags($label);
            }

            $status = $this->_objectManager->create(Status::class)->load($statusCode);
            // check if status exist
            if ($isNew && $status->getStatus()) {
                $this->messageManager
                    ->addErrorMessage(__('We found another order status with the same order status code.'));
                $this->_getSession()->setFormData($data);
                return $resultRedirect->setPath('sales/*/new');
            }
            $status->setData($data)->setStatus($statusCode);

            try {
                $status->save();
                $this->messageManager->addSuccessMessage(__('You saved the order status.'));
                return $resultRedirect->setPath('sales/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,

                    __('We can\'t add the order status right now.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $this->getRedirect($resultRedirect, $isNew);
        }
        return $resultRedirect->setPath('sales/*/');
    }

    /**
     * @param Redirect $resultRedirect
     * @param bool $isNew
     * @return Redirect
     */
    private function getRedirect(Redirect $resultRedirect, $isNew)
    {
        if ($isNew) {
            return $resultRedirect->setPath('sales/*/new');
        } else {
            return $resultRedirect->setPath('sales/*/edit', ['status' => $this->getRequest()->getParam('status')]);
        }
    }
    private function uploadImage()
    {

    }
}
