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
 * @package     Mageplaza_SeoDashboard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoDashboard\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Duplicate
 * @package Mageplaza\SeoDashboard\Block\Adminhtml
 */
class Duplicate extends Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_duplicate';
        $this->_blockGroup = 'Mageplaza_SeoDashboard';
        $this->_headerText = __('Duplicate Content');
        parent::_construct();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->removeButton('add');

        return parent::_prepareLayout();
    }
}
