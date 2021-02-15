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

class Delete extends \Bss\Popup\Controller\Adminhtml\Popup
{

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('popup_id');
        if ($id) {
            $title = "";
            try {
                /** @var \Bss\Popup\Model\Popup $Popup */
                $popup = $this->popupFactory->create()->load($id);

                $title = $popup->getPopupName();

                $popup->delete();
                $this->messageManager->addSuccessMessage(__('The Pop-up has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_bss_popup_popup_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                $resultRedirect->setPath('bss_popup/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_bss_popup_file_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );

                $this->messageManager->addErrorMessage($e->getMessage());

                $resultRedirect->setPath('bss_popup/*/edit', ['popup_id' => $id]);
                return $resultRedirect;
            }
        }

        $this->messageManager->addErrorMessage(__('Pop-up to delete was not found.'));
        
        $resultRedirect->setPath('bss_popup/*/');
        return $resultRedirect;
    }

    /**
     * Check Rule
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_Popup::delete");
    }

}
