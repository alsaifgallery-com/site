<?php

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;
class Edit extends State
{

    /**
     * @return void
     */
    public function execute()
    {
        $stateId = $this->getRequest()->getParam('id');
        /** @var \Custom\City\Model\State $model */
        $model = $this->_stateFactory->create();

        if ($stateId) {
            $model->load($stateId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This state no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getStateData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('city_state', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage States'));

        return $resultPage;
    }
}