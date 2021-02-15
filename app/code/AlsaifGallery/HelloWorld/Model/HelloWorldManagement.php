<?php


namespace AlsaifGallery\HelloWorld\Model;

class HelloWorldManagement implements \AlsaifGallery\HelloWorld\Api\HelloWorldManagementInterface
{
   protected $_customerFactory;
    /**
     * {@inheritdoc}
     */
   public function __construct(\Magento\Customer\Model\CustomerFactory $customerFactory){
       $this->_customerFactory = $customerFactory;
   }
    public function getHelloWorld($param1)
    {
        $customer= $this->_customerFactory->create()->load($param1);
        return $customer->getEmail();
//        return 'hello,'.$param1.' Here I am ,hello from ';
    }
}
