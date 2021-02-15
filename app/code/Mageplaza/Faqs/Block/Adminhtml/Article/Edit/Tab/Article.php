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

namespace Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Store\Model\System\Store;
use Mageplaza\Faqs\Model\Config\Source\Visibility;

/**
 * Class Article
 * @package Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab
 */
class Article extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    public $wysiwygConfig;

    /**
     * @var Yesno
     */
    protected $_booleanOption;

    /**
     * @var Visibility
     */
    protected $_visibility;

    /**
     * @var Robots
     */
    protected $_metaRobotsOptions;

    /**
     * Article constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magento\Config\Model\Config\Source\Design\Robots $robots
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Mageplaza\Faqs\Model\Config\Source\Visibility $visibility
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesno,
        Robots $robots,
        Store $systemStore,
        Visibility $visibility,
        Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_booleanOption     = $yesno;
        $this->_metaRobotsOptions = $robots;
        $this->systemStore        = $systemStore;
        $this->_visibility        = $visibility;
        $this->wysiwygConfig      = $wysiwygConfig;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\Faqs\Model\Artile $article */
        $article = $this->_coreRegistry->registry('mageplaza_faqs_article');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');

        $fieldset = $form->addFieldset('base_fieldset', [
                'legend' => __('General'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField('name', 'text', [
                'name'     => 'name',
                'label'    => __('Question'),
                'title'    => __('Question'),
                'required' => true
            ]
        );

        $fieldset->addField('url_key', 'text', [
                'name'  => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key')
            ]
        );

        $fieldset->addField('article_content', 'editor', [
                'name'   => 'article_content',
                'label'  => __('Answer'),
                'title'  => __('Answer'),
                'config' => $this->wysiwygConfig->getConfig(['add_variables' => false, 'add_widgets' => false, 'add_directives' => true])
            ]
        );

        $fieldset->addField('categories_ids', '\Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab\Renderer\Category', [
                'name'  => 'categories_ids',
                'label' => __('Categories'),
                'title' => __('Categories'),
            ]
        );
        if (!$article->getCategoriesIds()) {
            $article->setCategoriesIds($article->getCategoryIds());
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

            if (!$article->hasData('store_ids')) {
                $article->setStoreIds(0);
            }
        }
        else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('visibility', 'select', [
                'name'   => 'visibility',
                'label'  => __('Visibility'),
                'title'  => __('Visibility'),
                'values' => $this->_visibility->toOptionArray()
            ]
        );

        $fieldset->addField('position', 'text', [
                'name'  => 'position',
                'label' => __("Position"),
                'title' => __("Position"),
            ]
        );
        if (!$article->hasData('position')) {
            $article->setPosition(0);
        }
        $customerFieldset = $form->addFieldset('customer_fieldset', [
                'legend' => __('Customer Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        $customerFieldset->addField('author_name', 'text', [
                'name'     => 'author_name',
                'label'    => __('Author Name'),
                'title'    => __('Author Name'),
                'required' => true
            ]
        );

        $customerFieldset->addField('author_email', 'text', [
                'name'     => 'author_email',
                'label'    => __('Author Email'),
                'title'    => __('Author Email'),
                'required' => true,
                'class'    => 'validate-email'
            ]
        );

        $customerFieldset->addField('email_notify', 'checkbox', [
                'name'     => 'email_notify',
                'label'    => __('Receive Email Notification'),
                'title'    => __('Receive Email Notification'),
                'checked'  => ($article->getEmailNotify()) ? true : false,
                'onchange' => 'this.value = this.checked ? 1 : 0;',
                'value'    => $article->getEmailNotify()
            ]
        );

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

        $form->addValues($article->getData());

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
