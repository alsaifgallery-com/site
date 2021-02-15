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
 * @package     Mageplaza_SeoCrosslinks
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoCrosslinks\Block\Adminhtml\Term\Edit;

use Magento\Backend\Block\Widget;

/**
 * Class Tabs
 * @package Mageplaza\SeoCrosslinks\Block\Adminhtml\Term\Edit
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends Widget\Tabs
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('term_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Term Information'));
    }
}
