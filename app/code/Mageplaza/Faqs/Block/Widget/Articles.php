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

namespace Mageplaza\Faqs\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Mageplaza\Faqs\Block\Category\View;

/**
 * Class Articles
 * @package Mageplaza\Faqs\Block\Widget
 */
class Articles extends View implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "Mageplaza_Faqs::widget/articles.phtml";

    /**
     * @return View|null|void
     */
    protected function _prepareLayout()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
}
