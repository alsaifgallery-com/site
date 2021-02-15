<?php

namespace AlsaifGallery\RestAPIConfig\Model;

use AlsaifGallery\RestAPIConfig\Helper\Data;

class PhoneVerifyManagement implements \AlsaifGallery\RestAPIConfig\Api\PhoneVerifyManagementInterface
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
     * @var  \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $data;
    protected $customerRepo;
    protected $addressRepo;

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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepo,
        \Magento\Framework\Escaper $escaper

    ) {
        $this->data = $data;

        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->customerRepo = $customerRepo;
        $this->addressRepo = $addressRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetCode($phoneCode, $phone)
    {

        $response = $this->data->sentVerifyCode($phoneCode . $phone);
        // $response = json_decode($response,true);
        $result = json_decode($response, true);
        $arr['phone'] = $phone;
        $arr['phone_code'] = $phoneCode;
        if (isset($result['success']) && (strtolower($result['success']) == "true")) {
            $arr['status'] = 'success';
            $arr['message_id'] = $result['data']['MessageID'];
            $arr['message_status'] = $result['data']['MessageStatus'];
        } else {
            $arr['status'] = 'failed';
            $arr['message'] = $result['message'];
            $arr['errorCode'] = $result['errorCode'];
        }
        return [$arr];

    }

    /**
     * {@inheritdoc}
     */
    public function getVerifyCode($customerId, $phoneCode = "", $phone, $code, $addressId = null)
    {
        $inputMobile = $phoneCode . $phone;
        $returnData = [
            "status" => false,
            "message" => __("Verfication faild")->render(),
        ];
        $address = $addressPhoneVerifiedAttr = null;
        $addressPhoneCode = $addressPhone = "";
        // get the address and set it's phone as verified
        if (!is_null($addressId)) {
            $returnData["address_id"] = $addressId;
            $address = $this->addressRepo->getById($addressId);
            if (!is_null($address) && $address->getId()) {
                if ($address->getCustomerId() != $customerId) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("This Address not found in currunt Customer Addresses.")
                    );
                }
                $addressMobile = "";
                $addressPhoneVerifiedAttr = $address->getCustomAttribute("phone_verified");
                $addressPhoneCodeAttr = $address->getCustomAttribute("phone_code");
                $addressPhone = $address->getTelephone();
                if (!is_null($addressPhoneCodeAttr)) {
                    $addressPhoneCode = $addressPhoneCodeAttr->getValue();
                }

                if (empty($addressPhone)) {
                    throw new \Magento\Framework\Exception\NoSuchEntityException(
                        __(
                            "Mobile phone is empty for this address."
                        )
                    );
                }

                // check phone
                $addressMobile = $addressPhoneCode . $addressPhone;

                if ($addressMobile != $inputMobile) {
                    throw new \Magento\Framework\Exception\NoSuchEntityException(
                        __(
                            "This address does not match provided Mobile."
                        )
                    );
                }
            }
        }

        // ------
        $response = $this->data->sentVerifyNumber($inputMobile, $code);
        $result = json_decode($response, true);
        if (!is_null($result) && (strtolower($result['success']) == "true")) {
            $returnData["status"] = true;
            $returnData["message"] = __("Verfication successed")->render();
            $address->setCustomAttribute("phone_verified", 1);
            $updatedAddress = $this->addressRepo->save($address);
            $updatedAddressPhoneVerified = $address->getCustomAttribute("phone_verified");
            if (is_null($updatedAddressPhoneVerified) || ($updatedAddressPhoneVerified->getValue() != '1')) {
                $returnData["error_message"] = __("Can not update address phone_verified")->render();
            }
            return [$returnData];
        } else {
            $returnData["message"] = $result['message'];
            $returnData["error_code"] = $result['errorCode'];
        }
        return [$returnData];
    }

}
