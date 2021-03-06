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
 * @package     Mageplaza_SeoUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoUltimate\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Mageplaza\SeoUltimate\Helper\Data as HelperConfig;

/**
 * Class SeoHrefLang
 * @package Mageplaza\SeoUltimate\Plugin
 */
class SeoHrefLang
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mageplaza\SeoUltimate\Helper\Config|HelperConfig
     */
    protected $helperConfig;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $storeManager;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * SeoHrefLang constructor.
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param HelperConfig $helperConfig
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     */
    function __construct(
        PageConfig $pageConfig,
        Http $request,
        HelperConfig $helperConfig,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder
    )
    {
        $this->pageConfig   = $pageConfig;
        $this->request      = $request;
        $this->helperConfig = $helperConfig;
        $this->storeManager = $storeManager;
        $this->urlFinder    = $urlFinder;
    }

    /**
     * @param \Magento\Framework\View\Page\Config\Renderer $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(\Magento\Framework\View\Page\Config\Renderer $subject, $result)
    {
        if (!$this->helperConfig->isEnableHrefLang()) {
            return $result;
        }

        $linksRelAlternate = '';
        $storeId           = $this->storeManager->getStore()->getId();
        $fullActionName    = $this->request->getFullActionName();
        if (
            $fullActionName == 'cms_index_index' ||
            ($fullActionName == 'catalog_product_view' && $this->helperConfig->isEnableForProduct($storeId)) ||
            ($fullActionName == 'catalog_category_view' && $this->helperConfig->isEnableForCategory($storeId)) ||
            ($fullActionName == 'cms_page_view' && $this->helperConfig->isEnableForPage($storeId))

        ) {
            $linksRelAlternate = '<!-- Hreflang tag by Mageplaza_SEO -->';

            $oldRewrite = $this->urlFinder->findOneByData([
                UrlRewrite::TARGET_PATH => ltrim($this->request->getPathInfo(), '/'),
                UrlRewrite::STORE_ID    => $storeId,
            ]);

            /** @var Store $store */
            foreach ($this->storeManager->getStores() as $store) {
                if ($oldRewrite) {
                    $rewrite = $this->urlFinder->findOneByData([
                        UrlRewrite::ENTITY_TYPE      => $oldRewrite->getEntityType(),
                        UrlRewrite::ENTITY_ID        => $oldRewrite->getEntityId(),
                        UrlRewrite::STORE_ID         => $store->getId(),
                        UrlRewrite::IS_AUTOGENERATED => 1,
                    ]);
                    if ($rewrite && $rewrite->getRequestPath() !== $oldRewrite->getRequestPath()) {
                        $storeUrl = $store->getUrl($rewrite->getRequestPath());
                    }
                }

                $storeUrl = $this->formatUrlByStore(isset($storeUrl) ? $storeUrl : $store->getCurrentUrl(true));
                $hrefLang = $this->helperConfig->getHrefLangByStore($store->getId());

                $linksRelAlternate .= '<link rel="alternate" href="' . $storeUrl . '" hreflang="' . $hrefLang . '">';
            }
        }

        return $result . $linksRelAlternate;
    }

    /**
     * Format url by store
     * @param $url
     * @return bool|mixed|string
     */
    public function formatUrlByStore($url)
    {
        $position = strpos($url, '?');
        if ($position !== false) {
            $url = substr($url, 0, $position);
        }

        return rtrim($url, '/');
    }
}
