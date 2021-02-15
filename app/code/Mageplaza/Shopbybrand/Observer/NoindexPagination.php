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
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config;
use Mageplaza\Shopbybrand\Helper\Data;

/**
 * Class NoindexPagination
 *
 * @package Mageplaza\Shopbybrand\Observer
 */
class NoindexPagination implements ObserverInterface
{
    /**
     * @type \Mageplaza\Shopbybrand\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Page\Config $_corePageConfig ;
     */
    protected $_corePageConfig;

    /**
     * NoindexPagination constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\View\Page\Config $_corePageConfig
     * @param \Mageplaza\Shopbybrand\Helper\Data $helper
     */
    public function __construct(
        Http $request,
        Config $_corePageConfig,
        Data $helper
    ) {
        $this->_corePageConfig = $_corePageConfig;
        $this->_helper = $helper;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $actionName = $this->request->getFullActionName();
        if ($this->_helper->getModuleConfig('brand_seo/seo_pages') && $actionName == 'mpbrand_index_view' && $this->request->getParam('p')) {
            $this->_corePageConfig->setRobots('NOINDEX,FOLLOW');
        }
    }
}
