<?php


namespace AlsaifGallery\ForgotPassword\Api\Data;

interface ForgotPasswordInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const REQUEST_TOKEN = 'request_token';
    const REQUEST_NUMBER = 'request_number';
    const REQUEST_TOKEN_STATUS = 'request_token_status';
    const FORGOTPASSWORD_ID = 'forgotpassword_id';
    const TYPE = 'type';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
    const VERIFY_CODE = 'verify_code';
    const VERIFY_CODE_STATUS = 'verify_code_status';
    const SEND_STATUS = 'send_status';

    /**
     * Get forgotpassword_id
     * @return string|null
     */
    public function getForgotpasswordId();

    /**
     * Set forgotpassword_id
     * @param string $forgotpasswordId
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setForgotpasswordId($forgotpasswordId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordExtensionInterface $extensionAttributes
    );

    /**
     * Get request_number
     * @return string|null
     */
    public function getRequestNumber();

    /**
     * Set request_number
     * @param string $requestNumber
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestNumber($requestNumber);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get verify_code
     * @return string|null
     */
    public function getVerifyCode();

    /**
     * Set verify_code
     * @param string $verifyCode
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setVerifyCode($verifyCode);

    /**
     * Get verify_code_status
     * @return string|null
     */
    public function getVerifyCodeStatus();

    /**
     * Set verify_code_status
     * @param string $verifyCodeStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setVerifyCodeStatus($verifyCodeStatus);

    /**
     * Get request_token
     * @return string|null
     */
    public function getRequestToken();

    /**
     * Set request_token
     * @param string $requestToken
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestToken($requestToken);

    /**
     * Get request_token_status
     * @return string|null
     */
    public function getRequestTokenStatus();

    /**
     * Set request_token_status
     * @param string $requestTokenStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setRequestTokenStatus($requestTokenStatus);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setType($type);

    /**
     * Get send_status
     * @return string|null
     */
    public function getSendStatus();

    /**
     * Set send_status
     * @param string $sendStatus
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     */
    public function setSendStatus($sendStatus);
}
