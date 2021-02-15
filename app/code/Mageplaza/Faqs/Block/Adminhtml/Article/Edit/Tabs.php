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

namespace Mageplaza\Faqs\Block\Adminhtml\Article\Edit;

/**
 * Class Tabs
 * @package Mageplaza\Faqs\Block\Adminhtml\Article\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('article_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Question Information'));
    }

    /**
     * @inheritdoc
     *
     * @return \Magento\Backend\Block\Widget\Tabs
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
                'label'   => __('General'),
                'title'   => __('General'),
                'content' => $this->getChildHtml('main'),
                'active'  => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
