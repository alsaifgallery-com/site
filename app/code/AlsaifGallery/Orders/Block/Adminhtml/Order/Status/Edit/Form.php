<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AlsaifGallery\Orders\Block\Adminhtml\Order\Status\Edit;
use Magento\Sales\Model\Order\Status;
/**
 * Description of Form
 *
 * @author nada
 */

class Form extends \Magento\Sales\Block\Adminhtml\Order\Status\NewStatus\Form{
    protected $status;
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    /**
     * Modify structure of new status form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $status = $objectManager->create(Status::class)->load($this->getRequest()->getParam('status'));
        $form = $this->getForm();
        $form->setData('enctype', 'multipart/form-data');
        $fieldset = $form->addFieldset('base_fieldset_balleh', ['legend' => __('GStore Information')]);
        $fieldset->addField(
                'is_balleh',
                'select',
                ['name' => 'is_balleh', 'label' => __('Is Ours?'), 'value' => $status->getData('is_balleh'), 'options' => ['1' => __('Enabled'), '0' => __('Disabled')]]
        );
        $fieldset->addField(
                'sort_order',
                'text',
                array(
                    'name' => 'sort_order',
                    'label' => __('Sort Order'),
                    'title' => __('Sort Order'),
                    'required' => false,
                    'class' => 'validate-number',
                    'value' => $status->getData('sort_order')
                )
        );
        $fieldset->addField(
                'icon',
                'image',
                [
                    'title' => __('Icon'),
                    'label' => __('Icon'),
                    'name' => 'icon',
                    'note' => 'Allow image type: jpg, jpeg, gif, png',
                    'value'=>$status->getData('icon')
                ]
        );
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('sales/order_status/save', ['status' => $this->getRequest()->getParam('status')])
        );
        return $this;
    }
}
