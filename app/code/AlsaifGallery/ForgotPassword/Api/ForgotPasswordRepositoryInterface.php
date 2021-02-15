<?php


namespace AlsaifGallery\ForgotPassword\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ForgotPasswordRepositoryInterface
{

    /**
     * Save forgotPassword
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
    );

    /**
     * Retrieve forgotPassword
     * @param string $forgotpasswordId
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($forgotpasswordId);

    /**
     * Retrieve forgotPassword Records
     * @param string $forgotpasswordVerifyCode
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByVerifyCode($forgotpasswordVerifyCode);

    /**
     * Retrieve forgotPassword Records
     * @param string $forgotpasswordToken
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByToken($forgotpasswordToken);

    /**
     * Retrieve forgotPassword matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete forgotPassword
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
    );

    /**
     * Delete forgotPassword by ID
     * @param string $forgotpasswordId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($forgotpasswordId);
    
}
