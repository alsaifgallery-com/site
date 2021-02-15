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

namespace Mageplaza\Faqs\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\TreeFactory;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Config\Source\System\LinkPosition;

/**
 * Class Topmenu
 * @package Mageplaza\Faqs\Plugin
 */
class Topmenu
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\Data\TreeFactory
     */
    protected $_treeFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Topmenu constructor.
     * @param Data $helperData
     * @param TreeFactory $treeFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Data $helperData,
        TreeFactory $treeFactory,
        RequestInterface $request
    )
    {
        $this->_helperData  = $helperData;
        $this->_treeFactory = $treeFactory;
        $this->_request     = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return array
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    )
    {

        if ($this->_helperData->isEnabled()
            && strpos($this->_helperData->getFaqsPageConfig('link'), (string) LinkPosition::CATEGORY) !== false
            && $this->_helperData->getFaqsPageConfig('route')
            && $this->_helperData->isEnabledFaqsPage()) {
            $subject->getMenu()
                ->addChild(
                    new Node(
                        $this->_getMenuAsArray(),
                        'id',
                        $this->_treeFactory->create()
                    )
                );
        }

        return [$outermostClass, $childrenWrapClass, $limit];
    }

    /**
     * @return array
     */
    private function _getMenuAsArray()
    {
        $identifier = trim($this->_request->getPathInfo(), '/');
        $routePath  = explode('/', $identifier);
        $routeSize  = sizeof($routePath);
        $title      = $this->_helperData->getFaqsPageConfig('title');

        return [
            'name'       => $title ?: __('Faqs'),
            'id'         => 'mpfaqs-node',
            'url'        => $this->_helperData->getFaqsUrl(''),
            'has_active' => ($identifier == 'mpfaqs/article/index'),
            'is_active'  => ('mpfaqs' == array_shift($routePath)) && ($routeSize == 3)
        ];
    }
}
