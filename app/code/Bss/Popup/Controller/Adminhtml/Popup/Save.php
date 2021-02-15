<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Popup
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Popup\Controller\Adminhtml\Popup;

class Save extends \Bss\Popup\Controller\Adminhtml\Popup
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;


    /**
     * Constructor
     *
     * @param \Bss\Popup\Model\PopupFactory $popupFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Bss\Popup\Model\PopupFactory $popupFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->backendSession = $context->getSession();
        parent::__construct($popupFactory, $registry, $context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('popup');
        $data = $this->filterPostData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $popup = $this->_initPopup();

            $fromDate = $data['display_from'];
            $toDate = $data['display_to'];

            if (!$this->validateRangeDate($fromDate, $toDate)) {
                $this->messageManager->addErrorMessage(__('You must set the Start Date sooner than the End Date.'));
                $this->_getSession()->setBssPopupPopupData($data);
                $resultRedirect->setPath(
                    'bss_popup/*/edit',
                    [
                        'popup_id' => $popup->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }

            $popup->setData($data);
            
            try {
                $popup->save();

                $this->messageManager->addSuccessMessage(__('The Pop-up has been saved.'));
                $this->backendSession->setBssPopupPopupData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'bss_popup/*/edit',
                        [
                            'popup_id' => $popup->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }

                $resultRedirect->setPath('bss_popup/*/');
                return $resultRedirect;

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setBssPopupPopupData($data);
            $resultRedirect->setPath(
                'bss_popup/*/edit',
                [
                    'popup_id' => $popup->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }

        $resultRedirect->setPath('bss_popup/*/');
        return $resultRedirect;
    }

    /**
     * Filter Request Post Data
     *
     * @param array $postData
     * @return array
     */
    protected function filterPostData($postData)
    {
        $postData['storeview'] = !empty($postData['storeview'])? implode($postData['storeview'], ",") : "";
        $postData['customer_group'] = !empty($postData['customer_group'])?
                                        implode($postData['customer_group'], ","): "";
        $postData['page_display'] = !empty($postData['page_display'])? implode($postData['page_display'], ",") : "";
        return $postData;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return bool
     */
    protected function validateRangeDate($fromDate, $toDate)
    {
        $fromDateNumber = $this->convertToTime($fromDate);
        $toDateNumber = $this->convertToTime($toDate);
        if ((int)$toDateNumber - (int)$fromDateNumber >= 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $timeString
     * @return false|int
     */
    protected function convertToTime($timeString)
    {
        $time = strtotime($timeString);
        return $time;
    }

    /**
     * Check Rule
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_Popup::save");
    }

}
