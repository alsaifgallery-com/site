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
 * @package     Mageplaza_SeoUrl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoUrl\App\Request;

use Magento\Store\App\Request\PathInfoProcessor as StorePathInfoProcessor;
use \Magento\Store\App\Request\StorePathInfoValidator;
use Mageplaza\SeoUrl\Helper\Data;

/**
 * Class PathInfoProcessor
 * @package Mageplaza\SeoUrl\App\Request
 */
class PathInfoProcessor extends StorePathInfoProcessor
{
    /**
     * @type Data
     */
    protected $helper;

    /**
     * @param StorePathInfoValidator $StorePathInfoValidator
     * @param Data $helper
     */
    public function __construct(
        StorePathInfoValidator $StorePathInfoValidator,
        \Magento\Store\App\Request\StorePathInfoValidator $storePathInfoValidator,
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        Data $helper
    )
    {
        $this->helper = $helper;
        $this->storePathInfoValidator = $storePathInfoValidator;
        $this->config = $config;
        parent::__construct($StorePathInfoValidator, $config);
    }

    /**
     * Process path info
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\Framework\App\RequestInterface $request, $pathInfo) : String
    {
        // $pathInfo = parent::process($request, $pathInfo);

        if ((bool)$this->config->getValue(\Magento\Store\Model\Store::XML_PATH_STORE_IN_URL)) {
            $storeCode = $this->storePathInfoValidator->getValidStoreCode($request, $pathInfo);
            if (!empty($storeCode)) {
                if (!$request->isDirectAccessFrontendName($storeCode)) {
                    $pathInfo = $this->trimStoreCodeFromPathInfo($pathInfo, $storeCode);
                } else {
                    //no route in case we're trying to access a store that has the same code as a direct access
                    $request->setActionName(\Magento\Framework\App\Router\Base::NO_ROUTE);
                }
            }
        }

        $decodeUrl = $this->helper->decodeFriendlyUrl($pathInfo);
        if (!$decodeUrl) {
            return $pathInfo;
        }

        $requestUri = $request->getRequestUri();
        $requestUri .= strpos($requestUri, '?') ? '&' : '?';
        foreach ($decodeUrl['params'] as $key => $param) {
            $requestUri .= $key . '=' . $param . '&';
        }
        $request->setRequestUri(trim($requestUri, '&'));

        return $decodeUrl['pathInfo'];
    }

    /**
     * Trim store code from path info string if exists
     *
     * @param string $pathInfo
     * @param string $storeCode
     * @return string
     */
    private function trimStoreCodeFromPathInfo(string $pathInfo, string $storeCode) : ?string
    {
        if (substr($pathInfo, 0, strlen('/' . $storeCode)) == '/'. $storeCode) {
            $pathInfo = substr($pathInfo, strlen($storeCode)+1);
        }
        return empty($pathInfo) ? '/' : $pathInfo;
    }
}
