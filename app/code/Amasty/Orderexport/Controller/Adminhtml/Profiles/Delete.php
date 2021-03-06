<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderexport
 */

namespace Amasty\Orderexport\Controller\Adminhtml\Profiles;

class Delete extends \Amasty\Orderexport\Controller\Adminhtml\Profiles
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Amasty\Orderexport\Model\Profiles');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('This profile is deleted.'));
                $this->_redirect('amasty_orderexport/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('amasty_orderexport/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->messageManager->addError(__('Can\'t find a item to delete.'));
        $this->_redirect('amasty_orderexport/*/');
    }
}
