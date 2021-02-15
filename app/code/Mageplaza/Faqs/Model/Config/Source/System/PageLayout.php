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

namespace Mageplaza\Faqs\Model\Config\Source\System;

use Magento\Cms\Model\Page\Source\PageLayout as BasePageLayout;

/**
 * Class PageLayout
 * @package Mageplaza\Faqs\Model\Config\Source\System
 */
class PageLayout extends BasePageLayout
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        foreach ($options as $key => $layout) {
            if ($layout['value'] == 'empty') {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
