<?php

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Helper\Data;
use Custom\City\Model\ZipFactory;

abstract class Zip extends Action
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
     * @var \Custom\City\Model\ZipFactory
     */
    protected $_zipFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $dataHelper
     * @param \Custom\City\Model\CityFactory $cityFactory
     * @param ZipFactory $zipFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $dataHelper,
        \Custom\City\Model\CityFactory $cityFactory,
        ZipFactory $zipFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_zipFactory = $zipFactory;
        $this->_cityFactory = $cityFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
    }

}
