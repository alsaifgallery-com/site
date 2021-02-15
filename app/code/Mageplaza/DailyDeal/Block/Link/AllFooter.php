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
 * @package     Mageplaza_DailyDeal
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DailyDeal\Block\Link;

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\DailyDeal\Block\Pages\AllDeals;
use Mageplaza\DailyDeal\Model\Config\Source\ShowLinks;

/**
 * Class AllFooter
 * @package Mageplaza\DailyDeal\Block\Link
 */
class AllFooter extends Current
{
    /**
     * @var AllDeals
     */
    protected $_allDeals;

    /**
     * AllFooter constructor.
     *
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param AllDeals $allDeals
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        AllDeals $allDeals,
        array $data = []
    ) {
        $this->_allDeals = $allDeals;

        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_allDeals->isEnable() && $this->_allDeals->canShowLink(ShowLinks::FOOTER_LINK)) {
            $this->setData([
                'label' => $this->_allDeals->getPageTitle(),
                'path'  => $this->_allDeals->getRoute(),
            ]);
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_allDeals->getPageUrl();
    }
}
