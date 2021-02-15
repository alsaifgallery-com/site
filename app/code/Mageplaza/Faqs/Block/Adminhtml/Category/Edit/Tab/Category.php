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

namespace Mageplaza\Faqs\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Category
 * @package Mageplaza\Faqs\Block\Adminhtml\Category\Edit\Tab
 */
class Category extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    public $wysiwygConfig;

    /**
     * Yes/no options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $booleanOptions;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enableDisable;

    /**
     * @var Robots
     */
    protected $_metaRobotsOptions;

    /**
     * Category constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Robots $robots
     * @param Enabledisable $enableDisable
     * @param Store $systemStore
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Robots $robots,
        Enabledisable $enableDisable,
        Store $systemStore,
        Data $helperData,
        array $data = []
    )
    {
        $this->wysiwygConfig      = $wysiwygConfig;
        $this->booleanOptions     = $booleanOptions;
        $this->_metaRobotsOptions = $robots;
        $this->_enableDisable     = $enableDisable;
        $this->systemStore        = $systemStore;
        $this->helperData         = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\Faqs\Model\Category $category */
        $category = $this->_coreRegistry->registry('mageplaza_faqs_category');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('category_');
        $form->setFieldNameSuffix('category');

        $fieldset = $form->addFieldset('base_fieldset', [
                'legend' => __('General'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField('name', 'text', [
                'name'     => 'name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField('url_key', 'text', [
                'name'  => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key')
            ]
        );

        $fieldset->addField('enabled', 'select', [
                'name'   => 'enabled',
                'label'  => __('Status'),
                'title'  => __('Status'),
                'values' => $this->_enableDisable->toOptionArray()
            ]
        );
        if (!$category->hasData('enabled')) {
            $category->setEnabled(1);
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $fieldset->addField('store_ids', 'multiselect', [
                'name'     => 'store_ids',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$category->hasData('store_ids')) {
                $category->setStoreIds(0);
            }
        }
        else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('icon', 'text', [
                'name'  => 'icon',
                'label' => __('Icon'),
                'title' => __('Icon'),
                'note'  => '<div id="category-icon-image" title="Icon Image"><i class="fa fa-folder-open"><img src="' . $this->getViewFileUrl('Mageplaza_Faqs::media/images/icon-loader.gif') . '"></i></div>'
            ]
        );
        if (!$category->hasData('icon')) {
            $category->setIcon('fa fa-folder-open');
        }

        $fieldset->addField('position', 'text', [
                'name'  => 'position',
                'label' => __("Position"),
                'title' => __("Position"),
            ]
        );
        if (!$category->hasData('position')) {
            $category->setPosition(0);
        }

        $seoFieldset = $form->addFieldset('seo_fieldset', [
                'legend' => __('SEO Config'),
                'class'  => 'fieldset-wide'
            ]
        );

        $seoFieldset->addField('meta_title', 'text', [
                'name'  => 'meta_title',
                'label' => __('Meta Title'),
                'title' => __('Meta Title')
            ]
        );

        $seoFieldset->addField('meta_description', 'textarea', [
                'name'  => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description')
            ]
        );

        $seoFieldset->addField('meta_keywords', 'textarea', [
                'name'  => 'meta_keywords',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords')
            ]
        );

        $seoFieldset->addField('meta_robots', 'select', [
                'name'   => 'meta_robots',
                'label'  => __('Meta Robots'),
                'title'  => __('Meta Robots'),
                'values' => $this->_metaRobotsOptions->toOptionArray()
            ]
        );

        $form->addValues($category->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
