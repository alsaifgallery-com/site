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

namespace Mageplaza\Faqs\Block\Adminhtml\Article;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Mageplaza\Faqs\Block\Adminhtml\Article
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Article edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mageplaza_Faqs';
        $this->_controller = 'adminhtml_article';

        parent::_construct();

        $this->buttonList->add(
            'save-and-continue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        /** @var \Mageplaza\Faqs\Model\Article $article */
        $article = $this->coreRegistry->registry('mageplaza_faqs_article');
        if ($article->getId()) {
            return __("Edit '%1'", $this->escapeHtml($article->getName()));
        }

        return __('New Article/Question');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var \Mageplaza\Faqs\Model\Article $article */
        $article = $this->coreRegistry->registry('mageplaza_faqs_article');
        if ($id = $article->getId()) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }

        return parent::getFormActionUrl();
    }
}
