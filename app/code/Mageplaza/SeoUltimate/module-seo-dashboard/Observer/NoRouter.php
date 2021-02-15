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

namespace Mageplaza\SeoDashboard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\SeoDashboard\Model\NoRouteFactory;

/**
 * Class NoRouter
 * @package Mageplaza\SeoDashboard\Observer
 */
class NoRouter implements ObserverInterface
{
    /**
     * @type \Mageplaza\SeoDashboard\Model\NoRouterFactory
     */
    protected $_noRouteFactory;

    /**
     * Constructor
     *
     * @param \Mageplaza\SeoDashboard\Model\NoRouteFactory $noRouterFactory
     */
    function __construct(NoRouteFactory $noRouterFactory)
    {
        $this->_noRouteFactory = $noRouterFactory;
    }

    /**
     * Get all 404 routers
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @type \Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getRequest();
        $uri     = $request->getUri();

        $noRoute = $this->_noRouteFactory->create();
        if (!$noRoute->getCollection()->addFieldToFilter('uri', $uri)->getSize()) {
            $noRoute->setData([
                'uri' => $uri
            ])->save();
        }

        return $this;
    }
}