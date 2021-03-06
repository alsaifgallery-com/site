<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Tutorial
 *
 * @package Amasty\PageSpeedOptimizer
 */
class Tutorial extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getElementHtml(AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Amasty_PageSpeedOptimizer::tutorial.phtml');

        return $block->toHtml();
    }
}
