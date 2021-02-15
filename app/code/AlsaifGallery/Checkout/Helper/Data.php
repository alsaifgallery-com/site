<?php

namespace AlsaifGallery\Checkout\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $request;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    public function getCardToken(){
        return $this->request->getHeader('card-token');
    }
    public function getCreditToken(){
        return $this->request->getHeader('credit-token');
    }

}
