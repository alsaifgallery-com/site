<?php

namespace AlsaifGallery\RestAPIConfig\Model;

use AlsaifGallery\RestAPIConfig\Helper\Data;

class GetCodeManagement implements \AlsaifGallery\RestAPIConfig\Api\GetCodeManagementInterface
{

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'test/email/send_email';
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * {@inheritdoc}
     */
    public $data;

    /**
     *
     * @param Data $data
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     */

    public function __construct(
        Data $data,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper

    ) {
        $this->data = $data;

        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;

    }
    public function getGetCode($mobile)
    {

        $response = $this->data->sentVerifyCode($mobile);
        // $response = json_decode($response,true);
        $result = json_decode($response, true);
//            var_dump($response);die;
        // var_dump( $response['success'] , strtolower( $response['success'] ), boolval($response['success']) , ( boolval($response['success']) == true )  );die;
        $arr['mobile'] = $mobile;
//            if ( strtolower( $response['success'] )  == "true" ) {
        if (isset($result['success']) && (strtolower($response['success']) == "true")) {
            $arr['status'] = 'success';
//                $arr['verify_id'] = $response['data']['VerifyID'];
            $arr['message_id'] = $response['data']['MessageID'];
            $arr['message_status'] = $response['data']['MessageStatus'];
        } else {
            $arr['status'] = 'failed';
            $arr['message'] = $response['message'];
            $arr['errorCode'] = $response['errorCode'];
        }
        return json_encode($arr);

    }

}
