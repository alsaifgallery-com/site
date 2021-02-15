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

namespace Mageplaza\Faqs\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Faqs\Model\ArticleFactory;
use Mageplaza\Faqs\Model\CategoryFactory;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\Faqs\Model\Config\Source\System\AddQuestion;

/**
 * Class Data
 * @package Mageplaza\Faqs\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'faqs';
    const TYPE_ARTICLE       = 'article';
    const TYPE_CATEGORY      = 'category';

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var ArticleFactory
     */
    protected $_articleFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var TranslitUrl
     */
    public $translitUrl;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filter\TranslitUrl $translitUrl
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Mageplaza\Faqs\Model\ArticleFactory $articleFactory
     * @param \Mageplaza\Faqs\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TranslitUrl $translitUrl,
        FilterProvider $filterProvider,
        TransportBuilder $transportBuilder,
        CustomerSession $customerSession,
        ArticleFactory $articleFactory,
        CategoryFactory $categoryFactory
    )
    {
        $this->translitUrl       = $translitUrl;
        $this->_filterProvider   = $filterProvider;
        $this->_transportBuilder = $transportBuilder;
        $this->_customerSession  = $customerSession;
        $this->_articleFactory   = $articleFactory;
        $this->_categoryFactory  = $categoryFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Generate url_key for article
     *
     * @param $resource
     * @param $object
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateUrlKey($resource, $object, $name)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->translitUrl->filter($name);
            if ($urlKey) {
                $urlKey = $urlKey . ($attempt ?: '');
            }
        } while ($this->checkUrlKey($resource, $object, $urlKey));

        return $urlKey;
    }

    /**
     * Check url key if it is exist
     *
     * @param $resource
     * @param $object
     * @param $urlKey
     * @return bool
     */
    public function checkUrlKey($resource, $object, $urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select  = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('url_key = :url_key');

        $binds = ['url_key' => (string) $urlKey];

        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int) $id;
        }

        $result = $adapter->fetchOne($select, $binds);

        return $result;
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getFaqsPageConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('faq_home_page' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getProductTabConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('product_tab' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getTermConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('term_condition' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getEmailConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('email' . $code, $storeId);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getRoute($store = null)
    {
        return $this->getFaqsPageConfig('route', $store) ?: 'faq';
    }

    /**
     * @param null $store
     * @return string
     */
    public function getFaqsName($store = null)
    {
        return $this->getFaqsPageConfig('title', $store) ?: 'Frequently Answer and Question';
    }

    /**
     * @param null $urlKey
     * @param null $type
     * @return string
     */
    public function getFaqsUrl($urlKey = null, $type = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url    = $this->getUrl($this->getRoute() . '/' . $urlKey);
        $url    = explode('?', $url);
        $url    = $url[0];

        return rtrim($url, '/');
    }

    /**
     * @param $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Get category collection
     *
     * @param null $storeId
     * @return \Mageplaza\Faqs\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection($storeId = null)
    {
        return $this->getObjectCollection(self::TYPE_CATEGORY, 'enabled', $storeId);
    }

    /**
     * Get article collection
     *
     * @param null $storeId
     * @return \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    public function getArticleCollection($storeId = null)
    {
        return $this->getObjectCollection(self::TYPE_ARTICLE, 'visibility', $storeId);
    }

    /**
     * @param $type
     * @param $enabled
     * @param null $storeId
     * @return mixed
     */
    public function getObjectCollection($type, $enabled, $storeId = null)
    {
        $collection = $this->getFactoryByType($type)
            ->create()
            ->getCollection()
            ->addFieldToFilter($enabled, 1)
            ->setOrder('position', 'asc');
        $this->addStoreFilter($collection, $storeId);

        return $collection;
    }

    /**
     * Filter by store
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param null $storeId
     * @return mixed
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection->addFieldToFilter('main_table.store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }

    /**
     * @param $catId
     * @param $where
     * @return \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    public function getArticleByCategory($catId, $where = null)
    {
        /** @var \Mageplaza\Faqs\Model\ResourceModel\Article\Collection $collection */
        $collection = $this->getArticleCollection();

        $collection->getSelect()
            ->joinLeft(['category' => $collection->getTable('mageplaza_faqs_article_category')],
                'main_table.article_id = category.article_id')->where('category.category_id = ' . $catId . ' ' . $where);

        return $collection;
    }

    /**
     * @param $value
     * @param null $code
     * @param null $type
     * @return mixed
     */
    public function getObjectByParam($value, $code = null, $type = null)
    {
        $object = $this->getFactoryByType($type)
            ->create()
            ->load($value, $code);

        return $object;
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getFactoryByType($type = null)
    {
        switch ($type) {
            case self::TYPE_CATEGORY:
                $object = $this->_categoryFactory;
                break;
            default:
                $object = $this->_articleFactory;
        }

        return $object;
    }

    /**
     * get category collection by post
     * @param $ids
     * @return array|string
     */
    public function getCategoriesByArticle($ids)
    {
        $collection = $this->getObjectCollection(self::TYPE_CATEGORY, 'enabled')
            ->addFieldToFilter('main_table.category_id', ['in' => $ids]);

        return $collection;
    }

    /**
     * Get is enabled faq detail page
     *
     * @return mixed
     */
    public function isEnabledDetailPage()
    {
        return $this->getConfigGeneral('question_detail_page/enabled');
    }

    /**
     * @return mixed
     */
    public function isEnabledFaqsPage()
    {
        return $this->getFaqsPageConfig('enabled');
    }

    /**
     * Check if this is home page
     *
     * @return mixed
     */
    public function isFaqsHomePage()
    {
        return $this->_request->getFullActionName() == 'mpfaqs_article_index';
    }

    /**
     * @param $store
     * @param $toEmail
     * @param $template
     * @param $vars
     * @param string $sender
     */
    public function sendMail($store, $toEmail, $template, $vars, $sender = 'general')
    {
        if ($toEmail) {
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions([
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $store->getId()
                ])
                ->setFrom($sender)
                ->addTo($toEmail)
                ->setTemplateVars($vars)
                ->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }
    }

    /**
     * Get date formatted
     *
     * @param $date
     * @param $dateType
     * @return string
     */
    public function getDateFormat($date, $dateType)
    {
        $dateTime = (new \DateTime($date, new \DateTimeZone('UTC')));
        $dateTime->setTimezone(new \DateTimeZone($this->getTimezone()));
        $dateFormat = $dateTime->format($dateType);

        return $dateFormat;
    }

    /**
     * get configuration zone
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * Check customer is logged in or not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @param $content
     * @return string
     * @throws \Exception
     */
    public function getPageFilter($content)
    {
        return $this->_filterProvider->getPageFilter()->filter($content);
    }

    /**
     * Check module is available
     *
     * @param $moduleName
     * @return bool
     */
    public function checkModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Escape string for the JavaScript context
     *
     * @param string $string
     * @return string
     * @since 100.2.0
     */
    public function escapeJs($string)
    {
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        return preg_replace_callback(
            '/[^a-z0-9,\._]/iSu',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) != 1) {
                    $chr = mb_convert_encoding($chr, 'UTF-16BE', 'UTF-8');
                    $chr = ($chr === false) ? '' : $chr;
                }
                return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
            },
            $string
        );
    }

    /**
     * Check is show question form
     *
     * @return bool
     */
    public function isShowForm()
    {
        $questionConfig = $this->getConfigGeneral('question/enabled');
        $isLoggedIn     = $questionConfig == AddQuestion::LOGGED && $this->isLoggedIn();

        return $isLoggedIn || $questionConfig == AddQuestion::YES;
    }
}
