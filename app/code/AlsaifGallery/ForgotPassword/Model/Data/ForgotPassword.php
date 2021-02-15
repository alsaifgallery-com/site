<?php


namespace AlsaifGallery\ForgotPassword\Model\Data;

use AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface;
use \Magento\Framework\Api\AttributeValueFactory;

class ForgotPassword extends \Magento\Framework\Api\AbstractExtensibleObject implements ForgotPasswordInterface
{
    protected  $customerRepo;
    
    /**
     * 
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo
     */
    
    public function __construct(
       \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
       \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
       AttributeValueFactory $attributeValueFactory,
        $data = []
    ){
        $this->customerRepo =  $customerRepo;
         parent::__construct($extensionFactory,$attributeValueFactory,$data);
    }

    
    /**
     * Get forgotpassword_id
     * @return string|null
     */
    public function getForgotpasswordId()
    {
        return $this->_get(self::FORGOTPASSWORD_ID);
    }

    /**
     * Set forgotpassword_id
     * @param string $forgotpasswordId
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setForgotpasswordId($forgotpasswordId)
    {
        return $this->setData(self::FORGOTPASSWORD_ID, $forgotpasswordId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
     * Get customer
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        $customerId = $this->getCustomerId();
        $customer = $this->customerRepo->getById($customerId);
        if( $customer->getId()){
            return $customer;
        }
        return null;
    }
    

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get request_number
     * @return string|null
     */
    public function getRequestNumber()
    {
        return $this->_get(self::REQUEST_NUMBER);
    }

    /**
     * Set request_number
     * @param string $requestNumber
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestNumber($requestNumber)
    {
        return $this->setData(self::REQUEST_NUMBER, $requestNumber);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get verify_code
     * @return string|null
     */
    public function getVerifyCode()
    {
        return $this->_get(self::VERIFY_CODE);
    }

    /**
     * Set verify_code
     * @param string $verifyCode
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setVerifyCode($verifyCode)
    {
        return $this->setData(self::VERIFY_CODE, $verifyCode);
    }

    /**
     * Get verify_code_status
     * @return string|null
     */
    public function getVerifyCodeStatus()
    {
        return $this->_get(self::VERIFY_CODE_STATUS);
    }

    /**
     * Set verify_code_status
     * @param string $verifyCodeStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setVerifyCodeStatus($verifyCodeStatus)
    {
        return $this->setData(self::VERIFY_CODE_STATUS, $verifyCodeStatus);
    }

    /**
     * Get request_token
     * @return string|null
     */
    public function getRequestToken()
    {
        return $this->_get(self::REQUEST_TOKEN);
    }

    /**
     * Set request_token
     * @param string $requestToken
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestToken($requestToken)
    {
        return $this->setData(self::REQUEST_TOKEN, $requestToken);
    }

    /**
     * Get request_token_status
     * @return string|null
     */
    public function getRequestTokenStatus()
    {
        return $this->_get(self::REQUEST_TOKEN_STATUS);
    }

    /**
     * Set request_token_status
     * @param string $requestTokenStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestTokenStatus($requestTokenStatus)
    {
        return $this->setData(self::REQUEST_TOKEN_STATUS, $requestTokenStatus);
    }

    /**
     * Get type
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Set type
     * @param string $type
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get send_status
     * @return string|null
     */
    public function getSendStatus()
    {
        return $this->_get(self::SEND_STATUS);
    }

    /**
     * Set send_status
     * @param string $sendStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setSendStatus($sendStatus)
    {
        return $this->setData(self::SEND_STATUS, $sendStatus);
    }
    
    public function setAllData( array $data){
        foreach ( $data as $key=>$value ){
          $this->setData( $key ,  $value );
        }
    }
}
