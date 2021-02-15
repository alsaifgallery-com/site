<?php
namespace Vnecoms\Sms\Controller\Geoip;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use GeoIp2\Database\Reader;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @param Context $context
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Vnecoms\Sms\Helper\Data $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $reader = new Reader($this->helper->getGeoIpDatabase());
        $countryCode = '';
        try{
            $record = $reader->country($this->getClientIp());
            /* $record = $reader->country('149.56.130.117'); */
            $countryCode = $record->country->isoCode;
        }catch(\Exception $e){
            
        }
        $response->setData(['country' => $countryCode]);
        if($callBack = $this->getRequest()->getParam('callback')){
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $content = '/**/typeof '.$callBack.' === \'function\' && '.$callBack.'('.$response->toJson().')';
            return $this->getResponse()->setBody($content);
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
    
    /**
     * Get client ip
     * 
     * @return string
     */
    protected function getClientIp(){
        $params = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        $ip = '';
        foreach($params as $param){
            $ip = $this->getRequest()->getServer($param);
            if($ip) break;
        }
        return $ip;
        
    }
}
