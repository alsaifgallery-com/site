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

namespace Mageplaza\Faqs\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Router
 * @package Mageplaza\Faqs\Controller
 */
class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    public $actionFactory;

    /**
     * @var
     */
    public $helperData;

    /**
     * @var
     */
    protected $_request;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param Data $helperData
     */
    public function __construct(
        ActionFactory $actionFactory,
        Data $helperData
    )
    {
        $this->actionFactory = $actionFactory;
        $this->helperData    = $helperData;
    }

    /**
     * @param $controller
     * @param $action
     * @param array $params
     * @return \Magento\Framework\App\ActionInterface
     */
    public function _forward($controller, $action, $params = [])
    {
        $this->_request->setControllerName($controller)
            ->setActionName($action)
            ->setPathInfo('/mpfaqs/' . $controller . '/' . $action);

        foreach ($params as $key => $value) {
            $this->_request->setParam($key, $value);
        }

        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $routePath  = explode('/', $identifier);
        $routeSize  = sizeof($routePath);

        if (!$routeSize || ($routeSize > 3) || (array_shift($routePath) != $this->helperData->getRoute())) {
            return null;
        }

        $request->setModuleName('mpfaqs')
            ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        $controller = array_shift($routePath);
        if (!$controller) {
            $request->setControllerName('article')
                ->setActionName('index')
                ->setPathInfo('/mpfaqs/article/index');

            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }
        $action = array_shift($routePath) ?: 'index';

        switch ($controller) {
            case 'article':
                $article = $this->helperData->getObjectByParam($action, 'url_key');
                $request->setParam('id', $article->getId());
                $action = 'view';
                break;
            case 'category':
                $category = $this->helperData->getObjectByParam($action, 'url_key', Data::TYPE_CATEGORY);
                $request->setParam('id', $category->getId());
                $action = 'view';
                break;
            default:
                $article = $this->helperData->getObjectByParam($controller, 'url_key');
                $request->setParam('id', $article->getId());
                $controller = 'article';
                $action     = 'view';
        }

        $request->setControllerName($controller)
            ->setActionName($action)
            ->setPathInfo('/mpfaqs/' . $controller . '/' . $action);

        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
}
