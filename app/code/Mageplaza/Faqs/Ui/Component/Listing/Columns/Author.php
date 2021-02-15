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

namespace Mageplaza\Faqs\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\AccountManagement;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Faqs\Model\ArticleFactory;

/**
 * Class Author
 * @package Mageplaza\Faqs\Ui\Component\Listing\Columns
 */
class Author extends Column
{
    /**
     * @var Customer
     */
    protected $_customerModel;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var AccountManagement
     */
    protected $_accountManagement;

    /**
     * @var ArticleFactory
     */
    protected $_articleFactory;

    /**
     * Author constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AccountManagement $accountManagement
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param Customer $customer
     * @param ArticleFactory $articleFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AccountManagement $accountManagement,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Customer $customer,
        ArticleFactory $articleFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->_customerModel     = $customer;
        $this->_accountManagement = $accountManagement;
        $this->_storeManager      = $storeManager;
        $this->_urlBuilder        = $urlBuilder;
        $this->_articleFactory    = $articleFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->_accountManagement->isEmailAvailable($item['author_email'], $websiteId)) {
                    $item[$this->getData('name')] = $item['author_name'] . '<br>(' . $item['author_email'] . ')';
                }
                else {
                    $item[$this->getData('name')] = $item['author_name']
                                                    . '<br>(<a href="'
                                                    . $this->_urlBuilder->getUrl('customer/index/edit', ['id' => $this->_customerModel->setWebsiteId($websiteId)->loadByEmail($item['author_email'])->getId(), 'active_tab' => 'review'])
                                                    . '" target="_blank">'
                                                    . $item['author_email']
                                                    . '</a>)';
                }
            }
        }
        return $dataSource;
    }
}
