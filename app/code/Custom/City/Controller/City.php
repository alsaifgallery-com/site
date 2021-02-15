<?php

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Helper\Data;
use Custom\City\Model\CityFactory;

abstract class City extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Custom\City\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Custom\City\Model\CityFactory
     */
    protected $_cityFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $dataHelper
     * @param CityFactory $cityFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $dataHelper,
        CityFactory $cityFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_cityFactory = $cityFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
    }

}
